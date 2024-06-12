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
 * Privacy implementation for local_ws_subir_imagen_curso.
 *
 * @package   local_ws_subir_imagen_curso
 * @copyright 2023 Alexis Chata
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ws_subir_imagen_curso\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Funciones de privacidad para el plugin.
 *
 * @package   local_ws_subir_imagen_curso
 * @copyright 2023 Alexis Chata
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // Este complemento no almacena ningún dato personal del usuario.
    \core_privacy\local\metadata\null_provider {

    /**
     * Obtenga el identificador de cadena de idioma con el idioma del componente
     * archivo para explicar por qué este complemento no almacena datos.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
