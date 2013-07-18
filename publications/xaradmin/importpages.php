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
 * manage publication types (all-in-one function for now)
 */
function publications_admin_importpages()
{
    if (!xarSecurityCheck('AdminPublications')) return;

    // Get parameters
    if(!xarVarFetch('basedir',    'isset', $basedir,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('filelist',   'isset', $filelist,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('refresh',    'isset', $refresh,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',       'isset', $ptid,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('content',    'isset', $content,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('title',      'isset', $title,       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',       'isset', $cids,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('filterhead', 'isset', $filterhead,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('filtertail', 'isset', $filtertail,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('findtitle',  'isset', $findtitle,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('numrules',   'isset', $numrules,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('search',     'isset', $search,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('replace',    'isset', $replace,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('test',       'isset', $test,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('import',     'isset', $import,      NULL, XARVAR_DONT_SET)) {return;}


    // Initialise the template variables
    $data = array();

    if (empty($basedir)) {
        $data['basedir'] = realpath(sys::code() . 'modules/publications');
    } else {
        $data['basedir'] = realpath($basedir);
    }

    $data['filelist'] = xarModAPIFunc('publications','admin','browse',
                                      array('basedir' => $data['basedir'],
                                            'filetype' => 'html?'));

    if (isset($refresh) || isset($test) || isset($import)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;
    }

    $data['authid'] = xarSecGenAuthKey();

    // Get current publication types
    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    $data['pubtypes'] = $pubtypes;
    $data['fields'] = array();
    $data['cats'] = array();
    if (!empty($ptid)) {
        $data['ptid'] = $ptid;

        $pubfields = xarModAPIFunc('publications','user','getpubfields');
        $pubfieldtypes = xarModAPIFunc('publications','user','getpubfieldtypes');
        $pubfieldformats = xarModAPIFunc('publications','user','getpubfieldformats');
        foreach ($pubfields as $field => $dummy) {
            if (($pubfieldtypes[$field] == 'text' || $pubfieldtypes[$field] == 'string') &&
                !empty($pubtypes[$ptid]['config'][$field]['label']) &&
                $pubtypes[$ptid]['config'][$field]['format'] != 'fileupload') {
                $data['fields'][$field] = $pubtypes[$ptid]['config'][$field]['label'] . ' [' .
                                          $pubfieldformats[$pubtypes[$ptid]['config'][$field]['format']] . ']';
            }
        }

        $catlist = array();
        $rootcats = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $ptid));
        foreach ($rootcats as $catid) {
            $catlist[$catid['category_id']] = 1;
        }
        $seencid = array();
        if (isset($cids) && is_array($cids)) {
            foreach ($cids as $catid) {
                if (!empty($catid)) {
                    $seencid[$catid] = 1;
                }
            }
        }
        $cids = array_keys($seencid);
        foreach (array_keys($catlist) as $catid) {
            $data['cats'][] = xarModAPIFunc('categories',
                                            'visual',
                                            'makeselect',
                                            Array('cid' => $catid,
                                                  'return_itself' => true,
                                                  'select_itself' => true,
                                                  'values' => &$seencid,
                                                  'multiple' => 1));
        }
    }

    $data['selected'] = array();
    if (!isset($refresh) && isset($filelist) && is_array($filelist) && count($filelist) > 0) {
        foreach ($filelist as $file) {
            if (!empty($file) && in_array($file,$data['filelist'])) {
                $data['selected'][$file] = 1;
            }
        }
    }

    if (isset($title) && isset($data['fields'][$title])) {
        $data['title'] = $title;
    }
    if (isset($content) && isset($data['fields'][$content])) {
        $data['content'] = $content;
    }

    if (!isset($filterhead)) {
        $data['filterhead'] = '#^.*<body[^>]*>#is';
    } else {
        $data['filterhead'] = $filterhead;
    }
    if (!isset($filtertail)) {
        $data['filtertail'] = '#</body.*$#is';
    } else {
        $data['filtertail'] = $filtertail;
    }
    if (!isset($findtitle)) {
        $data['findtitle'] = '#<title>(.*?)</title>#is';
    } else {
        $data['findtitle'] = $findtitle;
    }

    if (!isset($numrules)) {
        $numrules = 3;
    }
    $data['search'] = array();
    $data['replace'] = array();
    for ($i = 0; $i < $numrules; $i++) {
        if (isset($search[$i])) {
            $data['search'][$i] = $search[$i];
            if (isset($replace[$i])) {
                $data['replace'][$i] = $replace[$i];
            } else {
                $data['replace'][$i] = '';
            }
        } else {
            $data['search'][$i] = '';
            $data['replace'][$i] = '';
        }
    }

    if (isset($data['ptid']) && isset($data['content']) && count($data['selected']) > 0
        && (isset($test) || isset($import))) {

        $mysearch = array();
        $myreplace = array();
        for ($i = 0; $i < $numrules; $i++) {
            if (!empty($data['search'][$i])) {
                $mysearch[] = $data['search'][$i];
                if (!empty($data['replace'][$i])) {
                    $myreplace[] = $data['replace'][$i];
                } else {
                    $myreplace[] = '';
                }
            }
        }

        $data['logfile'] = '';
        foreach (array_keys($data['selected']) as $file) {
            $curfile = realpath($basedir . '/' . $file);
            if (!file_exists($curfile) || !is_file($curfile)) {
                continue;
            }
            $page = @join('', file($curfile));
            if (!empty($data['findtitle']) && preg_match($data['findtitle'],$page,$matches)) {
                $title = $matches[1];
            } else {
                $title = '';
            }
            if (!empty($data['filterhead'])) {
                $page = preg_replace($filterhead,'',$page);
            }
            if (!empty($data['filtertail'])) {
                $page = preg_replace($filtertail,'',$page);
            }
            if (count($mysearch) > 0) {
                $page = preg_replace($mysearch,$myreplace,$page);
            }

            $article = array('title' => ' ',
                             'summary' => '',
                             'body' => '',
                             'notes' => '',
                             'pubdate' => filemtime($curfile),
                             'state' => 2,
                             'ptid' => $data['ptid'],
                             'cids' => $cids,
                          // for preview
                             'pubtype_id' => $data['ptid'],
                             'owner' => xarUserGetVar('id'),
                             'id' => 0);
            if (!empty($data['title']) && !empty($title)) {
                $article[$data['title']] = $title;
            }
            $article[$data['content']] = $page;
            if (isset($test)) {
                // preview the first file as a test
                $data['preview'] = xarModFunc('publications','user','display',
                                              array('article' => $article, 'preview' => true));
                break;
            } else {
                $id = xarModAPIFunc('publications', 'admin', 'create', $article);
                if (empty($id)) {
                    return; // throw back
                } else {
                    $data['logfile'] .= xarML('File #(1) was imported as #(2) #(3)',$curfile,$pubtypes[$data['ptid']]['description'],$id);
                    $data['logfile'] .= '<br />';
                }
            }
        }
    }

    $data['filterhead'] = xarVarPrepForDisplay($data['filterhead']);
    $data['filtertail'] = xarVarPrepForDisplay($data['filtertail']);
    $data['findtitle'] = xarVarPrepForDisplay($data['findtitle']);
    for ($i = 0; $i < $numrules; $i++) {
        if (!empty($data['search'][$i])) {
            $data['search'][$i] = xarVarPrepForDisplay($data['search'][$i]);
        }
        if (!empty($data['replace'][$i])) {
            $data['replace'][$i] = xarVarPrepForDisplay($data['replace'][$i]);
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>