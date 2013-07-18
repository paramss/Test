<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * get array of configurable fields for publication types
 * @TODO : add dynamic fields here for .81+
 * @return array('title'   => array('label'  => xarML('...'),
                                    'format' => '...',
                                    'input'  => 1),
                 'summary' => array('label'  => xarML('...'),
                                    'format' => '...',
                                    'input'  => 1),
                 ...);
 */
function publications_userapi_getpubfields($args)
{
    return array(
        'title'    => array('label'  => xarML('Title'),
                            'format' => 'textbox',
                            'input'  => 1),
        'summary'  => array('label'  => xarML('Summary'),
                            'format' => 'textarea_medium',
                            'input'  => 1),
        'body' => array('label'  => xarML('Body'),
                            'format' => 'textarea_large',
                            'input'  => 1),
        'notes'    => array('label'  => xarML('Notes'),
                            'format' => 'textarea',
                            'input'  => 0),
        'owner' => array('label'  => xarML('Author'),
                            'format' => 'username',
                            'input'  => 0),
        'pubdate'  => array('label'  => xarML('Publication Date'),
                            'format' => 'calendar',
                            'input'  => 0),
        'state'   => array('label'  => xarML('Status'),
                            'format' => 'state',
                            'input'  => 0),
    );
}

?>
