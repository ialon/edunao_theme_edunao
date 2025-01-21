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
 * The report that displays the certificates the user has throughout the site.
 *
 * @package    mod_coursecertificate
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_edunao123\output\coursecertificate;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class for the report that displays the certificates the user has throughout the site.
 *
 * @package    mod_coursecertificate
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class my_certificates_table extends \tool_certificate\my_certificates_table {

    /**
     * @var int $userid The user id
     */
    protected $userid;

    /**
     * Sets up the table.
     *
     * @param int $userid
     * @param string|null $download The file type, null if we are not downloading
     */
    public function __construct($userid, $download = null) {
        parent::__construct('tool_certificate_my_certificates_table');

        $this->attributes['class'] = 'flexible table mycertificates';
        $this->userid = $userid;

        $columns = [
            'thumbnail',
            'nameandtime',
            'courselink',
            'download',
        ];
        $headers = [
            get_string('thumbnail', 'theme_edunao123'),
            get_string('nameandtime', 'theme_edunao123'),
            get_string('courselink', 'theme_edunao123'),
            get_string('file'),
        ];

        if ($this->show_share_on_linkedin()) {
            $columns[] = 'linkedin';
            $headers[] = get_string('shareonlinkedin', 'tool_certificate');
            $this->no_sorting('linkedin');
        }

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->column_class('thumbnail', 'thumbnail');
        $this->column_class('nameandtime', 'nameandtime');
        $this->column_class('courselink', 'icon');
        $this->column_class('download', 'icon');
        $this->column_class('linkedin', 'icon');
        $this->collapsible(false);
        $this->sortable(false);
        $this->no_sorting('thumbnail');
        $this->no_sorting('nameandtime');
        $this->no_sorting('courselink');
        $this->no_sorting('download');
        $this->is_downloadable(false);
    }

    /**
     * Generate the course thumbnail column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_thumbnail($certificate) {
        global $DB, $OUTPUT;

        // Prepare certificate icon.
        $pixurl = $OUTPUT->image_url('certificate', 'theme_edunao123');
        $pix = \html_writer::img($pixurl, 'certificate-icon');

        // Prepare background image.
        $courseimage = $OUTPUT->get_generated_image_for_id($certificate->id);
        if ($certificate->courseid) {
            $courseimage = \cache::make('core', 'course_image')->get($certificate->courseid);
        }
        $attrs = array('style' => 'background-image: url("' . $courseimage . '");');
        $output = \html_writer::div($pix, 'course-thumbnail', $attrs);

        if ($certificate->courseid) {
            // Obtain course directly from DB to allow missing courses.
            $course = $DB->get_record('course', ['id' => $certificate->courseid]);

            if ($course) {
                $courseurl = new \moodle_url('/course/view.php', ['id' => $certificate->courseid]);
                $innerhtml = '<span class="sr-only">' . $course->fullname . '</span>' . $pix;
                $thumbnail = \html_writer::div($innerhtml, 'course-thumbnail', $attrs);
                $output = \html_writer::link($courseurl, $thumbnail);
            }
        }

        return $output;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_nameandtime($certificate) {
        global $DB;

        $context = \context::instance_by_id($certificate->contextid);

        // Use certificate name as fallback
        $name = format_string($certificate->name, true, ['context' => $context]);

        if ($certificate->courseid) {
            // Obtain course directly from DB to allow missing courses.
            if ($course = $DB->get_record('course', ['id' => $certificate->courseid])) {
                $context = \context_course::instance($course->id);
                $name = format_string($course->fullname, true, ['context' => $context]);
            }
        }

        $nameandtime = \html_writer::div($name, 'cert-name');
        $date = userdate($certificate->timecreated, get_string('strftimedate', 'core_langconfig'));
        $nameandtime .= get_string('issuedon', 'theme_edunao123', $date);

        return $nameandtime;
    }

    /**
     * Generate the course link column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_courselink($certificate) {
        global $DB, $OUTPUT;

        $link = '';

        if ($certificate->courseid) {
            // Obtain course directly from DB to allow missing courses.
            if ($course = $DB->get_record('course', ['id' => $certificate->courseid])) {
                $icon = new \pix_icon('i/course', get_string('viewcourse', 'theme_edunao123'));
                $courseurl = new \moodle_url('/course/view.php', ['id' => $certificate->courseid]);

                $link = $OUTPUT->action_link($courseurl, '', null, ['target' => '_blank'], $icon);
            }
        }

        return $link;
    }

    /**
     * Generate the download column.
     *
     * @param \stdClass $issue
     * @return string
     */
    public function col_download($issue) {
        global $OUTPUT;

        $icon = new \pix_icon('download', get_string('downloadcertificate', 'theme_edunao123'), 'tool_certificate');
        $link = \tool_certificate\template::view_url($issue->code);

        return $OUTPUT->action_link($link, '', null, ['target' => '_blank'], $icon);
    }

    /**
     * Whether the LinkedIn column be shown
     *
     * @return bool
     */
    private function show_share_on_linkedin() {
        global $USER;
        return $USER->id == $this->userid && get_config('tool_certificate', 'show_shareonlinkedin');
    }

    /**
     * Outputs the HTML for the certificates table.
     *
     * This method captures the output of the `out` method and returns it as a string.
     *
     * @param int $perpage The number of items to display per page.
     * @return string The HTML output of the certificates table.
     */
    public function output_html($perpage) {
        $output = '';

        ob_start();
        $this->out($perpage, false);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
