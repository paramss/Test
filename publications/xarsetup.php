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
 * Initial setup for the publications module
 */

// TODO: load configuration from file(s) ?

    // Configuration of the different publication type fields
    // An empty label means it's (currently) not used for that type
    $config = array();

    $config['news'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Introduction'),
                         'format' => 'textarea_medium',
                         'input'  => 1),
        'body' => array('label'  => xarML('Body'),
                         'format' => 'textarea_large',
                         'input'  => 1),
        'notes' => array('label'  => xarML('Notes'),
                         'format' => 'textarea',
                         'input'  => 0),
        'owner' => array('label'  => xarML('Author'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Publication Date'),
                         'format' => 'calendar',
                         'input'  => 1),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );
    $config['docs'] = array(
        'title' => array('label'  => xarML('Subject'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'body' => array('label'  => xarML('Content'),
                         'format' => 'textupload',
                         'input'  => 1),
        'notes' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'owner' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'pubdate' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'status' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
    );
// TODO: adapt/evaluate for reviews
    $config['reviews'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Review'),
                         'format' => 'textarea_large',
                         'input'  => 1),
        'body' => array('label'  => xarML('Related Link'),
                         'format' => 'urltitle',
                         'input'  => 1),
        'notes' => array('label'  => xarML('Image'),
                         'format' => 'image',
                         'input'  => 1),
        'owner' => array('label'  => xarML('Reviewer'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Date'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );
    $config['faqs'] = array(
        'title' => array('label'  => xarML('Question'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Details'),
                         'format' => 'textarea',
                         'input'  => 1),
        'body' => array('label'  => xarML('Answer'),
                         'format' => 'textarea_large',
                         'input'  => 0),
        'notes' => array('label'  => xarML('Submitted by'),
                         'format' => 'textbox',
                         'input'  => 1),
        'owner' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'pubdate' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'status' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
    );
    $config['pictures'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Thumbnail'),
                         'format' => 'image',
                         'input'  => 1),
        'body' => array('label'  => xarML('Picture'),
                         'format' => 'image',
                         'input'  => 1),
        'notes' => array('label'  => xarML('Comments'),
                         'format' => 'textarea',
                         'input'  => 1),
        'owner' => array('label'  => xarML('Author'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Publication Date'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
    );
// TODO: add fields for editorials etc.
    $config['weblinks'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Description'),
                         'format' => 'textarea',
                         'input'  => 1),
        'body' => array('label'  => xarML('Website'),
                         'format' => 'url',
                         'input'  => 1),
        'notes' => array('label'  => xarML('Source'),
                         'format' => 'textbox',
                         'input'  => 1),
        'owner' => array('label'  => xarML('Submitter'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Submitted on'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );

    $config['quotes'] = array(
        'title' => array('label'  => xarML('Author'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Quotes'),
                         'format' => 'textarea',
                         'input'  => 1),
        'body' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'notes' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'owner' => array('label'  => xarML('Submitted By'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Submitted On'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );

    $config['downloads'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Summary'),
                         'format' => 'textarea',
                         'input'  => 1),
        'body' => array('label'  => '',
                         'format' => 'static',
                         'input'  => 0),
        'notes' => array('label'  => xarML('Upload File'),
                         'format' => 'fileupload',
                         'input'  => 1),
        'owner' => array('label'  => xarML('Author'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Submitted On'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );
/*
    $config['generic'] = array(
        'title' => array('label'  => xarML('Title'),
                         'format' => 'textbox',
                         'input'  => 1),
        'summary' => array('label'  => xarML('Summary'),
                         'format' => 'textarea_medium',
                         'input'  => 1),
        'body' => array('label'  => xarML('Body'),
                         'format' => 'textarea_large',
                         'input'  => 1),
        'notes' => array('label'  => xarML('Notes'),
                         'format' => 'textarea',
                         'input'  => 0),
        'owner' => array('label'  => xarML('Author'),
                         'format' => 'username',
                         'input'  => 0),
        'pubdate' => array('label'  => xarML('Publication Date'),
                         'format' => 'calendar',
                         'input'  => 0),
        'status' => array('label'  => xarML('Status'),
                         'format' => 'status',
                         'input'  => 0),
    );
*/

    // The list of currently supported publication types
    $pubtypes = array(
                    array(1, 'news', 'News Publications',
                          serialize($config['news'])),
                    array(2, 'docs', 'Documents',
                          serialize($config['docs'])),
                    array(3, 'reviews', 'Reviews',
                          serialize($config['reviews'])),
                    array(4, 'faqs', 'FAQs',
                          serialize($config['faqs'])),
                    array(5, 'pictures', 'Pictures',
                          serialize($config['pictures'])),
                    array(6, 'weblinks', 'Web Links',
                          serialize($config['weblinks'])),
                    array(7, 'quotes', 'Random Quotes',
                          serialize($config['quotes'])),
                    array(8, 'downloads', 'Downloads',
                          serialize($config['downloads'])),
              );

    // Some starting categories as an example
    $categories = array();

    $categories[] = array('name' => 'Generic1',
                          'description' => 'Generic Category 1',
                          'children' => array('Generic1 Sub1',
                                              'Generic1 Sub2'));
    $categories[] = array('name' => 'Generic2',
                          'description' => 'Generic Category 2',
                          'children' => array('Generic2 Sub1',
                                              'Generic2 Sub2'));
    $categories[] = array('name' => 'Topics',
                          'description' => 'News Topics',
                          'children' => array('Topic 1',
                                              'Topic 2'));
    $categories[] = array('name' => 'Categories',
                          'description' => 'News Categories',
                          'children' => array('Category 1',
                                              'Category 2'));
    $categories[] = array('name' => 'Sections',
                          'description' => 'Document Sections',
                          'children' => array('Section 1',
                                              'Section 2'));
    $categories[] = array('name' => 'FAQ',
                          'description' => 'Frequently Asked Questions',
                          'children' => array('FAQ Type 1',
                                              'FAQ Type 2'));
    $categories[] = array('name' => 'Gallery',
                          'description' => 'Picture Gallery',
                          'children' => array('Album 1',
                                              'Album 2'));
    $categories[] = array('name' => 'Web Links',
                          'description' => 'Web Link Categories',
                          'children' => array('Link Category 1',
                                              'Link Category 2'));
    $categories[] = array('name' => 'Random Quotes',
                          'description' => 'Random Quote Categories',
                          'children' => array('Quote Category 1',
                                              'Quote Category 2'));
    $categories[] = array('name' => 'Downloads',
                          'description' => 'Download Categories',
                          'children' => array('Download Category 1',
                                              'Download Category 2'));

    // publications settings for each publication type
    $settings = array();

// TODO: split into content- & publication-related settings in the future ?

    // news publications can be in old-style Topics & Categories, and in new Generic1
    $settings[1] = array('number_of_columns'    => 2,
                         'items_per_page'       => 10,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 1,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 0,
                         'show_archives'        => 1,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Topics',
                                                         'Categories',
                                                         'Generic1'));

    // section documents can be in old-style Sections, and in new Generic1
    $settings[2] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         // category name - will be replaced by 'c' . cid in xarinit.php
                         'defaultview'          => 'Sections',
                         'show_categories'      => 0,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 1,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 0,
                         'show_ratings'         => 0,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 1,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 2,
                         'defaultsort'          => 'title',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Sections',
                                                         'Generic1'));

    // reviews can be in new Generic1 (no categories in old-style reviews ?)
    $settings[3] = array('number_of_columns'    => 2,
                         'items_per_page'       => 20,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 1,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 1,
                         'show_archives'        => 1,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Generic1'));

    // faqs can be in old-style FAQs, and in new Generic1
    $settings[4] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         // category name - will be replaced by 'c' . cid in xarinit.php
                         'defaultview'          => 'FAQ',
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 0,
                         'show_ratings'         => 0,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 1,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 2,
                         'defaultsort'          => 'title',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('FAQ',
                                                         'Generic1'));

    // pictures can be in Gallery and new Generic1
    $settings[5] = array('number_of_columns'    => 3,
                         'items_per_page'       => 12,
                         'defaultview'          => 1,
                         'show_categories'      => 0,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 1,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 0,
                         'show_ratings'         => 1,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 1,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 2,
                         'defaultsort'          => 'date',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Gallery',
                                                         'Generic1'));

    // weblinks can be in old-style Web Links, and in new Generic1
    $settings[6] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 1,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date ASC',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Web Links',
                                                         'Generic1'));

    // quotes can be in Random Quotes and in new Generic1
    $settings[7] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 1,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date ASC',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Random Quotes',
                                                         'Generic1'));

    // downloads can be in Downloads and in new Generic1
    $settings[8] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 0,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 1,
                         'show_archives'        => 0,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date ASC',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Downloads',
                                                         'Generic1'));

    // default settings
    $settings[0] = array('number_of_columns'    => 0,
                         'items_per_page'       => 20,
                         'defaultview'          => 1,
                         'show_categories'      => 1,
                         'show_catcount'        => 0,
                         'show_prevnext'        => 0,
                         'show_comments'        => 1,
                         'show_keywords'        => 1,
                         'show_hitcount'        => 1,
                         'show_ratings'         => 0,
                         'show_archives'        => 1,
                         'show_map'             => 1,
                         'show_publinks'        => 0,
                         'show_pubcount'        => 1,
                         'do_transform'         => 0,
                         'title_transform'      => 0,
                         'prevnextart'          => 0,
                         'usealias'             => 0,
                         'page_template'        => '',
                         'defaultstatus'        => 0,
                         'defaultsort'          => 'date',
                         // category names - will be replaced by cids in xarinit.php
                         'categories'           => array('Generic1',
                                                         'Generic2'));

    // default publication type is news publications
    $defaultpubtype = 1;

?>
