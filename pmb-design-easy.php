<?php
/**
 * @package Hello_Dolly
 * @version 1.7.2
 */
/*
Plugin Name: Print My Blog - Sample Easy Design
Plugin URI: https://github.com/mnelson4/pmb-sample-design-easy
Description: A simple example design that does not support dividing projects into parts or front and back matter.
Author: Mike Nelson
Version: 1.0.0
Author URI:
*/

use PrintMyBlog\entities\DesignTemplate;
use PrintMyBlog\orm\entities\Design;
use Twine\forms\base\FormSection;
use Twine\forms\inputs\AdminFileUploaderInput;
use Twine\forms\inputs\ColorInput;
use Twine\forms\inputs\DatepickerInput;
use Twine\forms\inputs\TextAreaInput;
use Twine\forms\inputs\TextInput;
use Twine\forms\strategies\validation\TextValidation;

define('PMBE_MAIN_FILE', __FILE__);
define('PMBE_MAIN_DIR', __DIR__);
add_action( 'pmb_register_designs', 'pmbe_register_design', 1 );
register_activation_hook(PMBE_MAIN_FILE,'pmbe_activation');

/**
 * Called when this plugin is activated, and gets PMB to check this design exists in the database (and if not, adds it)
 */
function pmbe_activation(){
    pmbe_register_design();
    pmb_check_db();
}

/**
 * Registers the design and design template. This should be done on every request so PMB knows they exist.
 */
function pmbe_register_design() {
    if(! function_exists('pmb_register_design')){
        return;
    }
    pmb_register_design_template(
        'easy_template',
        function () {
            return [
                'title' => __('Easy Digital PDF'),
                'format' => 'digital_pdf',
                'dir' => PMBE_MAIN_DIR . '/design/',
                'default' => 'easy',
                'url' => plugins_url('design/', PMBE_MAIN_FILE),
                'docs' => '',
                'supports' => [
                ],
                'design_form_callback' => function () {
                    return (new FormSection([
                        'subsections' =>
                            [
                                'internal_footnote_text' => new TextInput([
                                    'html_label_text' => __('Internal Footnote Text', 'print-my-blog'),
                                    'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the page number.', 'print-my-blog'),
                                    'default' => __('See page %s.', 'print-my-blog'),
                                    'validation_strategies' => [
                                        new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'print-my-blog'), '~.*\%s.*~')
                                    ]
                                ]),
                                'footnote_text' => new TextInput([
                                    'html_label_text' => __('External Footnote Text', 'print-my-blog'),
                                    'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the URL', 'print-my-blog'),
                                    'default' => __('See %s.', 'print-my-blog'),
                                    'validation_strategies' => [
                                        new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'print-my-blog'), '~.*\%s.*~')
                                    ]
                                ])
                            ],
                    ]))->merge(pmb_generic_design_form());
                },
                'project_form_callback' => function (Design $design) {
                    return new FormSection([
                        'subsections' => [
                            'institution' => new TextInput(
                                [
                                    'html_label_text' => __('Issue', 'print-my-blog'),
                                    'html_help_text' => __('Text that appears at the top-right of the cover'),
                                ]
                            ),
                            'authors' => new TextAreaInput(
                                [
                                    'html_label_text' => __('ByLine', 'print-my-blog'),
                                    'html_help_text' => __('Project Author(s)', 'print-my-blog'),
                                ]
                            ),
                            'date' => new DatepickerInput([
                                'html_label_text' => __('Date Issued', 'print-my-blog'),
                                'html_help_text' => __('Text that appears under the byline', 'print-my-blog'),
                            ]),
                        ]
                    ]);
                }
            ];
        }
    );
    pmb_register_design(
        'easy_template',
    'easy',
        function (DesignTemplate $design_template) {
            $preview_folder_url = plugins_url('/design/assets/', PMBE_MAIN_FILE);
            return [
                'title' => __('Easy Academic Paper', 'print-my-blog'),
                'quick_description' => __('An easy design', 'print-my-blog'),
                'description' => pmb_get_contents(PMBE_MAIN_DIR . '/design/description.php'),
                'author' => [
                    'name' => 'Mike Nelson',
                    'url' => 'https://printmy.blog'
                ],
                'previews' => [
                    [
                        'url' => $preview_folder_url . '/preview1.jpg',
                        'desc' => __('Title page, showing the double-spaced text.', 'print-my-blog')
                    ],
                    [
                        'url' => $preview_folder_url . '/preview2.jpg',
                        'desc' => __('Main matter, showing smaller images and double-spaced text.', 'print-my-blog')
                    ]
                ],
                'design_defaults' => [
                    'custom_css' => ''
                ],
                'project_defaults' => [
                    'institution' => 'Print My Blog'
                ],
            ];
        }
    );
}