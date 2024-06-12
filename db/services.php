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
 * Web service definitions for local_ws_subir_imagen_curso
 *
 * @package    local_ws_subir_imagen_curso
 * @copyright  2023 Alexis Chata
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_ws_subir_imagen_curso' => array(
        'classname' => 'local_ws_subir_imagen_curso_external',
        'methodname' => 'ws_subir_imagen_curso',
        'classpath' => 'local/ws_subir_imagen_curso/externallib.php',
        'description' => 'Subir imagen de curso',
        'type' => 'read',
        'capabilities' => '',
    ),
);
