<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service library functions
 *
 * @package    local_ws_subir_imagen_curso
 * @copyright  2023 Alexis Chata
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/filelib.php");
require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * Web service API definition.
 *
 * @package local_ws_subir_imagen_curso
 * @copyright 2023 Alexis Chata
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_ws_subir_imagen_curso_external extends external_api
{
    
    /**
     * Returns description of ws_subir_imagen_curso parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function ws_subir_imagen_curso_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'context id', VALUE_DEFAULT, null),
            'component' => new external_value(PARAM_COMPONENT, 'component'),
            'filearea'  => new external_value(PARAM_AREA, 'file area'),
            'itemid'    => new external_value(PARAM_INT, 'associated id'),
            'filepath'  => new external_value(PARAM_PATH, 'file path'),
            'filename'  => new external_value(PARAM_FILE, 'file name'),
            'filecontent' => new external_value(PARAM_TEXT, 'file content'),
            'contextlevel' => new external_value(
                PARAM_ALPHA,
                'The context level to put the file in,
                (block, course, coursecat, system, user, module)',
                VALUE_DEFAULT,
                null
            ),
            'instanceid' => new external_value(PARAM_INT, 'The Instance id of item associated
            with the context level', VALUE_DEFAULT, null),
            'courseid'    => new external_value(PARAM_INT, 'course id'),
        ]);
    }

    /**
     * ws_subir_imagen_curso a file to moodle
     *
     * @param int    $contextid    context id
     * @param string $component    component
     * @param string $filearea     file area
     * @param int    $itemid       item id
     * @param string $filepath     file path
     * @param string $filename     file name
     * @param string $filecontent  file content
     * @param string $contextlevel Context level (block, course, coursecat, system, user or module)
     * @param int    $instanceid   Instance id of the item associated with the context level
     * @return array
     * @since Moodle 2.2
     */
    public static function ws_subir_imagen_curso(
        $contextid,
        $component,
        $filearea,
        $itemid,
        $filepath,
        $filename,
        $filecontent,
        $contextlevel,
        $instanceid,
        $courseid
    ) {
        global $USER, $CFG, $DB;

        $fileinfo = self::validate_parameters(self::ws_subir_imagen_curso_parameters(), [
            'contextid' => $contextid, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid,
            'filepath' => $filepath, 'filename' => $filename, 'filecontent' => $filecontent, 'contextlevel' => $contextlevel,
            'instanceid' => $instanceid,'courseid' => $courseid,
        ]);

        if (!isset($fileinfo['filecontent'])) {
            throw new moodle_exception('nofile');
        }
        // Saving file.
        $dir = make_temp_directory('wsupload');

        if (empty($fileinfo['filename'])) {
            $filename = uniqid('wsupload', true).'_'.time().'.tmp';
        } else {
            $filename = $fileinfo['filename'];
        }

        if (file_exists($dir.$filename)) {
            $savedfilepath = $dir.uniqid('m').$filename;
        } else {
            $savedfilepath = $dir.$filename;
        }

        file_put_contents($savedfilepath, base64_decode($fileinfo['filecontent']));
        @chmod($savedfilepath, $CFG->filepermissions);
        unset($fileinfo['filecontent']);

        if (!empty($fileinfo['filepath'])) {
            $filepath = $fileinfo['filepath'];
        } else {
            $filepath = '/';
        }

        // Only allow uploads to draft area.
        if (!($fileinfo['component'] == 'user' and $fileinfo['filearea'] == 'draft')) {
            throw new coding_exception('File can be uploaded to user draft area only');
        } else {
            $component = 'user';
            $filearea = $fileinfo['filearea'];
        }

        $itemid = 0;
        if (isset($fileinfo['itemid'])) {
            $itemid = $fileinfo['itemid'];
        }
        if ($filearea == 'draft' && $itemid <= 0) {
            // Generate a draft area for the files.
            $itemid = file_get_unused_draft_itemid();
        } else if ($filearea == 'private') {
            // TODO MDL-31116 in user private area, itemid is always 0.
            $itemid = 0;
        }

        // We need to preserve backword compatibility. Context id is no more a required.
        if (empty($fileinfo['contextid'])) {
            unset($fileinfo['contextid']);
        }

        // Get and validate context.
        $context = self::get_context_from_params($fileinfo);
        self::validate_context($context);
        if (($fileinfo['component'] == 'user' and $fileinfo['filearea'] == 'private')) {
            throw new moodle_exception('privatefilesupload');
        }

        $browser = get_file_browser();

        // Check existing file.
        if ($file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            throw new moodle_exception('fileexist');
        }

        // Move file to filepool.
        if ($dir = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, '.')) {
            $info = $dir->create_file_from_pathname($filename, $savedfilepath);
            $params = $info->get_params();
            unlink($savedfilepath);

            // get contextid of course
            // Cache all courses.
            $courses = array();
            list($sqlcourseids, $paramsql) = $DB->get_in_or_equal($courseid, SQL_PARAMS_NAMED);
            $cselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
            $cjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
            $paramsql['contextlevel'] = CONTEXT_COURSE;
            $coursesql = "SELECT c.* $cselect
                            FROM {course} c $cjoin
                            WHERE c.id $sqlcourseids";
            $rs = $DB->get_recordset_sql($coursesql, $paramsql);
            
            foreach ($rs as $course) {
                $course_contextid = $course->ctxid;
                // Adding course contexts to cache.
                context_helper::preload_from_record($course);
                // Cache courses.
                $courses[$course->id] = $course;
            }
            $rs->close();
            // end - get contextid of course

            $DB->delete_records('files', ['contextid' => $course_contextid, 'filearea' => 'overviewfiles', 'component' => 'course']);
            
            $fs = get_file_storage();
            $file = $fs->get_file(
                $params['contextid'],
                $params['component'],
                $params['filearea'],
                $params['itemid'],
                $params['filepath'],
                $params['filename'],);
                
                $filerecord = [
            'contextid'    => $course_contextid,
            'component'    => 'course',
            'filearea'     => 'overviewfiles',
            'itemid'       => 0,
            'filepath'     => '/',
            'filename'     => $params['filename'],
            'timecreated'  => time(),
            'timemodified' => time(),
            ];
            $fs->create_file_from_storedfile($filerecord, $file);
            // Now delete the file.
            $file->delete();
            $DB->delete_records('files', ['contextid' => $params['contextid'], 'itemid' => $params['itemid'], 'filearea' => 'draft', 'component' => 'user']);

            return [
                'contextid' => $params['contextid'],
                'component' => $params['component'],
                'filearea' => $params['filearea'],
                'itemid' => $params['itemid'],
                'filepath' => $params['filepath'],
                'filename' => $params['filename'],
                'url' => $info->get_url(),
            ];
        } else {
            throw new moodle_exception('nofile');
        }
    }

    /**
     * Returns description of ws_subir_imagen_curso returns
     *
     * @return external_single_structure
     * @since Moodle 2.2
     */
    public static function ws_subir_imagen_curso_returns() {
        return new external_single_structure([
            'contextid' => new external_value(PARAM_INT, ''),
            'component' => new external_value(PARAM_COMPONENT, ''),
            'filearea'  => new external_value(PARAM_AREA, ''),
            'itemid'   => new external_value(PARAM_INT, ''),
            'filepath' => new external_value(PARAM_TEXT, ''),
            'filename' => new external_value(PARAM_FILE, ''),
            'url'      => new external_value(PARAM_TEXT, ''),
        ]);
    }
}

