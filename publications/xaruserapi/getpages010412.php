<?php

// Get all matching pages.
// Retrieve the page type details here too
// itemid: page ID (optional)
// pids: list of page ID (optional)
// name: page name
// itemtype: page itemtype
// parent: page parent (0=root page)
// left_range: tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// left_exclude: prune tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// key: string indicates the key used for pages ('id', 'index', 'name')
// dd_flag: include dynamic data if available (default true)
// count: boolean return just a count of records if true
// tree_contains_name: limit the search to the tree of pages containing the page(s) of the given name
// tree_contains_id: limit the search to the tree of pages containing the page of the given ID
// tree_ancestors: boolean, when fetching trees, will limit to just ancestors (and self) of the page name or ID

function publications_userapi_getpages($args)
{
    extract($args);

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();

    $where = array();
    $bind = array();

    // Default dynamic data retrieval to true.
    if (!isset($dd_flag)) {
        $dd_flag = true;
    }

    // Possible values for the array key. Defaults to index (count incrementing from zero)
    // Note: 'name' may not be unique, but all the others are.
    if (!xarVarValidate('enum:id:index:name:left:right', $key, true)) {$key = 'index';}

    if (isset($baseonly)) {
        $where[] = 'tpages.parent_id = ?';
        $bind[] = 0;
    }

    if (isset($name)) {
        $where[] = 'tpages.name = ?';
        $bind[] = (string)$name;
    }

    if (isset($status)) {
        // If a list of statuses have been provided, then select for any of them.
        if (strpos($status, ',') === false) {
            $where[] = "tpages.state = ?";
            $numeric_status = convert_status($status);
            $bind[] = strtoupper($status);
        } else {
            $statuses = explode(',', strtoupper($status));
            $numeric_statuses = array();
            foreach ($statuses as $stat) $numeric_statuses[] = convert_status($stat);
            $where[] = "tpages.state IN (?" . str_repeat(",?", count($numeric_statuses)-1) . ')';            
            $bind = array_merge($bind, $numeric_statuses);
        }
    }

    if (isset($id)) {
        $where[] = 'tpages.id = ?';
        $bind[] = (int)$id;
    } elseif (!empty($ids)) {
        $addwhere = array();
        foreach ($ids as $myid) {
            if (!empty($myid) && is_numeric($myid)) {
                $addwhere[] = '?';
                $bind[] = (int)$myid;
            }
        }
        if (!empty($addwhere)) {
            $where[] = 'tpages.id IN (' . join(', ', $addwhere) . ')';
        }
    }

    if (isset($itemtype)) {
        $where[] = 'tpages.pubtype_id = ?';
        $bind[] = (int)$itemtype;
    }

    if (isset($parent)) {
        $where[] = 'tpages.parentpage_id = ?';
        $bind[] = (int)$parent;
    }

    // Used to retrieve descendants.
    if (isset($left_range) && is_array($left_range)) {
        $where[] = 'tpages.leftpage_id between ? AND ?';
        $bind[] = (int)$left_range[0];
        $bind[] = (int)$left_range[1];
    }

    // Used to prune a single branch of the tree.
    if (isset($left_exclude) && is_array($left_exclude)) {
        //'tpages.leftpage_id NOT between ? AND ?' - does not work on some databases
        $where[] = '(tpages.leftpage_id < ? OR tpages.leftpage_id > ?)';
        $bind[] = (int)$left_exclude[0];
        $bind[] = (int)$left_exclude[1];
    }

    // Used to retrieve ancestors.
    if (isset($wrap_range) && is_numeric($wrap_range)) {
        $where[] = 'tpages.leftpage_id <= ? AND tpages.rightpage_id >= ?';
        $bind[] = (int)$wrap_range;
        $bind[] = (int)$left_range;
    }

    // We may just want a count.
    if (!empty($count)) {
        $count = true;
    } else {
        $count = false;
    }

    // The ordering is important for later processing, since these
    // pages are always represented as an hierarchy.
    if ($count) {
        $query = 'SELECT COUNT(*)';
    } else {
        // The DISTINCT is needed in case use of 'tree_contains_name'
        // matches more than one page with the same name. Page names
        // are not unique.
        $query = 'SELECT DISTINCT
            tpages.id,
            tpages.name,
            tpages.title,
            tpages.pubtype_id,
            tpages.parent_id,
            tpages.locale,
            tpages.leftpage_id,
            tpages.rightpage_id,
            tpages.parentpage_id,
            tpages.access,
            tpages.state,
            pt.description
            
        ';
    }

    $query .= ' FROM ' . $xartable['publications'] . ' AS tpages INNER JOIN ' . $xartable['publications_types'] . ' AS pt ON pt.id = tpages.pubtype_id ';

    // If the request is to fetch a tree that *contains* a particular
    // page, then add the extra sub-queries in here.

    if (!empty($tree_contains_id) || !empty($tree_contains_name)) {
        // Join to get the member page.
        $query .= ' INNER JOIN ' . $xartable['publications'] . ' AS tpages_member';

        if (!empty($tree_contains_id)) {
            $query .= ' ON tpages_member.id = ?';
            array_unshift($bind, (int)$tree_contains_id);
        }

        if (!empty($tree_contains_name)) {
            $query .= ' ON tpages_member.name = ?';
            array_unshift($bind, (string)$tree_contains_name);
        }

        if (!empty($tree_ancestors)) {
            // We don't want the complete tree for the matching pages - just
            // their ancestors. This is useful for checking paths, without
            // fetching complete trees.
            $query .= ' AND tpages_member.leftpage_id BETWEEN tpages.leftpage_id AND tpages.rightpage_id';
        } else {
            // Join to find the root page of the tree containing the required page.
            // This matches the complete tree for the root under the selected page.
            $query .= ' INNER JOIN ' . $xartable['publications'] . ' AS tpages_root'
                . ' ON tpages_root.leftpage_id <= tpages_member.leftpage_id'
                . ' AND tpages_root.rightpage_id >= tpages_member.rightpage_id'
                . ' AND tpages.leftpage_id BETWEEN tpages_root.leftpage_id AND tpages_root.rightpage_id'
                . ' AND tpages_root.parentpage_id = 0';
        }
    }

    $query .= (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . (empty($count) ? ' ORDER BY tpages.leftpage_id ASC' : '');

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    if (xarUserGetVar('uname') == 'adminx') {
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->addtable($xartable['publications'], 'tpages');
    $q->addtable($xartable['publications_types'], 'pt');
    $q->join('pt.id', 'tpages.pubtype_id');
    $q->setdistinct();
    $q->addfield('tpages.id AS id');
    $q->addfield('tpages.name AS name');
    $q->addfield('tpages.title AS title');
    $q->addfield('tpages.pubtype_id AS ptid');
    $q->addfield('tpages.parent_id AS parent_id');
    $q->addfield('tpages.locale AS locale');
    $q->addfield('tpages.leftpage_id AS left');
    $q->addfield('tpages.rightpage_id AS right');
    $q->addfield('tpages.parentpage_id AS parent');
    $q->addfield('tpages.access AS access');
    $q->addfield('tpages.state AS status');
    $q->addfield('pt.description AS description');
    
    if (isset($baseonly)) $q->eq('tpages.parent_id', 0);
    if (isset($name)) $q->eq('tpages.name', (string)$name);
    if (isset($status)) {
        // If a list of statuses have been provided, then select for any of them.
        if (strpos($status, ',') === false) {
            $numeric_status = convert_status($status);
            $q->eq('tpages.state', strtoupper($status));
        } else {
            $statuses = explode(',', strtoupper($status));
            $numeric_statuses = array();
            foreach ($statuses as $stat) $numeric_statuses[] = convert_status($stat);
            $q->in('tpages.state', $numeric_statuses);
        }
    }
    if (isset($id)) {
        $q->eq('tpages.id', (int)$id);
        $where[] = 'tpages.id = ?';
        $bind[] = (int)$id;
    } elseif (!empty($ids)) {
        $addwhere = array();
        foreach ($ids as $myid) {
            if (!empty($myid) && is_numeric($myid)) {
                $addwhere[] = (int)$myid;
            }
        }
        $q->in('tpages.state', $addwhere);
    }
    if (isset($itemtype)) $q->eq('tpages.pubtype_id', (int)$itemtype);
    if (isset($parent)) $q->eq('tpages.parentpage_id', (int)$parent);
    // Used to retrieve descendants.
    if (isset($left_range) && is_array($left_range)) {
        $q->between('tpages.leftpage_id', $left_range);
    }
    // Used to prune a single branch of the tree.
    if (isset($left_exclude) && is_array($left_exclude)) {
        //'tpages.leftpage_id NOT between ? AND ?' - does not work on some databases
        $c[] = $q->plt('tpages.leftpage_id',(int)$left_exclude[0]);
        $c[] = $q->pgt('tpages.leftpage_id',(int)$left_exclude[1]);
        $q->qor($c);
        unset($c);
    }
    // Used to retrieve ancestors.
    if (isset($wrap_range) && is_numeric($wrap_range)) {
        $c[] = $q->ple('tpages.leftpage_id',(int)$wrap_range[0]);
        $c[] = $q->pge('tpages.leftpage_id',(int)$left_range[1]);   // can't be right: this is an array
        $q->qand($c);
        unset($c);
    }    

    // If the request is to fetch a tree that *contains* a particular
    // page, then add the extra sub-queries in here.
    if (!empty($tree_contains_id) || !empty($tree_contains_name)) {
        $q->addtable($xartable['publications'], 'tpages_member');
        
        if (!empty($tree_contains_id)) $q->eq('tpages_member.id', (int)$tree_contains_id);
        if (!empty($tree_contains_name)) $q->eq('tpages_member.name', (int)$tree_contains_name);
        if (!empty($tree_ancestors)) {
            // We don't want the complete tree for the matching pages - just
            // their ancestors. This is useful for checking paths, without
            // fetching complete trees.
            $q->between('tpages_member.leftpage_id', 'expr:tpages.leftpage_id AND tpages.rightpage_id');
        } else {
            // Join to find the root page of the tree containing the required page.
            // This matches the complete tree for the root under the selected page.
            $q->addtable($xartable['publications'], 'tpages_root');
            $q->le('tpages_root.leftpage_i', 'expr:tpages_member.leftpage_id');
            $q->ge('tpages_root.rightpage_id', 'expr:tpages_member.rightpage_id');
            $q->between('tpages.leftpage_id', 'expr:tpages_root.leftpage_id AND tpages_root.rightpage_id');
            $q->eq('tpages_root.parentpage_id', 0);
        }
    }
    if(!empty($count)) $q->setorder('tpages.leftpage_id', 'ASC');
    $q->qecho();
    }

    if ($count) {
        if ($result->EOF) {
            $pages = 0;
        } else {
            list($pages) = $result->fields;
        }
    } else {
        $index = 0;
        $id2key = array();
        $pages = array();

        // Get all the page type details.
        $pagetypes = xarMod::apiFunc(
            'publications', 'user', 'get_pubtypes',
            array('key' => 'id', 'dd_flag' => $dd_flag)
        );

        while (!$result->EOF) {
            list(
            $id,
            $name,
            $title,
            $pubtype_id,
            $base_id,
            $locale,
            $leftpage_id,
            $rightpage_id,
            $parentpage_id,
            $access,
            $state,
            $pubtype_name
            ) = $result->fields;

            // Fetch the next record as soon as we have the value, so
            // we can skip pages more easily.
            $result->MoveNext();

            $id = (int)$id;

            // At this point check the privileges of the page fetched.
            // To prevent broken trees, if a page is not assessible, prune
            // (ie discard) descendant pages of that page. Descendants will have
            // a left value between the left and right values of the
            // inaccessible page.

            if (!empty($prune_left)) {
                if ($leftpage_id <= $prune_left) {
                    // The current page is still a descendant of the unprivileged page.
                    continue;
                } else {
                    // We've reached a non-descendant - stop pruning now.
                    $prune_left = 0;
                }
            }

            // JDJ 2008-06-11: now only need ViewPublicationsPage to be able to select the page,
            // but ReadPublicationsPage to actually read it.
            // The lowest privilege will be inherited, so one page with only View privilege
            // will cause all descendent pages to have, at most, view privilege.
            // We still need to fetch full details of these view-only pages, but we must flag
            // then up in some way (status?). Displaying any of these pages would instead just
            // show the 'no privs' page.

            // Define admin access
            sys::import('modules.dynamicdata.class.properties.master');
            $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
            $typename = $pagetypes[$pubtype_id]['name'];
            $args = array(
                'instance' => $name . ":" . $typename,
                'level' => 800,
            );
            $adminaccess = $accessproperty->check($args);

            $info = unserialize($access);
            if (!empty($info['view_access'])) {
                // Decide whether the current user can create blocks of this type
                $args = array(
                    'module' => 'publications',
                    'component' => 'Page',
                    'instance' => $name . ":" . $typename,
                    'group' => $info['view_access']['group'],
                    'level' => $info['view_access']['level'],
                );
                if (!$accessproperty->check($args)) {
                    // Save the right value. We need to skip all subsequent
                    // pages until we get to a page to the right of this one.
                    // The pages will be in 'left' order, so the descendants
                    // will be contiguous and will immediately follow this page.
                    $prune_left = $rightpage_id;
                    // Don't get this unless you are an admin
                    if (!$adminaccess) continue;
                }
            }

            if (!empty($overview_only_left) && $left <= $overview_only_left) {
                // We have got past the overview-only page, so can reset the flag.
                $overview_only_left = 0;
            }

            if (!empty($info['display_access'])) {
                $args = array(
                    'module' => 'publications',
                    'component' => 'Page',
                    'instance' => $name . ":" . $typename,
                    'group' => $info['display_access']['group'],
                    'level' => $info['display_access']['level'],
                );
                if (!$accessproperty->check($args)) {
                    // We have reached a page that allows only overview access.
                    // Flag all pages with the restricted view until we get past this page.
                    $overview_only_left = $right;
                    // Don't get this unless you are an admin
                    if (!$adminaccess) continue;
                }
            }

            if (!xarSecurityCheck('ReadPublications', 0, 'Page', $name . ':' . $typename, 'publications')) {
                // We have reached a page that allows only overview access.
                // Flag all pages with the restricted view until we get past this page.
                $overview_only_left = $rightpage_id;
            }

            // Note: ['parent_id'] is the parent page ID,
            // but ['parent'] is the parent item key in the
            // pages array.
            $id2key[(int)$id] = $$key;
            if ($key == 'id') {
                $parent_key = (int)$parentpage_id;
            } else {
                if (isset($id2key[$parentpage_id])) {
                    $parent_key = $id2key[$parentpage_id];
                } else {
                    $parent_key = 0;
                }
            }
            $pages[$$key] = array(
                'id' => $id,
                'key' => $$key,
                'name' => $name,
                'title' => $title,
                'ptid' => (int)$pubtype_id,
                'base_id' => (int)$base_id,
                'locale' => $locale,
                'parent_key' => $parent_key,
                'parent_id' => (int)$parentpage_id,
                'left' => (int)$leftpage_id,
                'right' => (int)$rightpage_id,
                'access' => unserialize($access),
                'status' => $state,
                'pubtype_name' => $pubtype_name,
            );
            $index += 1;
        }
    }
    return $pages;
}

function convert_status($status)
{
    switch ($status)
    {
        case 'DELETED': return 0;
        case 'INACTIVE': return 1;
        case 'DRAFT': return 2;
        case 'ACTIVE': return 3;
        case 'FRONTPAGE': return 4;
        case 'PLACEHOLDER': return 5;
    }
}

?>