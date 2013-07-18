<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    function publications_user_clone()
    {
        if (!xarSecurityCheck('ModeratePublications')) return;

        if(!xarVarFetch('name',     'isset', $objectname,      NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('ptid',     'isset', $ptid,            NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('itemid',   'isset', $data['itemid'],  NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('confirm',  'int',   $confirm,         0, XARVAR_DONT_SET)) {return;}


        if (empty($data['itemid'])) return xarResponse::NotFound();

        // If a pubtype ID was passed, get the name of the pub object
        if (isset($ptid)) {
            $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
            $pubtypeobject->getItem(array('itemid' => $ptid));
            $objectname = $pubtypeobject->properties['name']->value;
        }
        if (empty($objectname)) return xarResponse::NotFound();

        sys::import('modules.dynamicdata.class.objects.master');
        $data['object'] = DataObjectMaster::getObject(array('name' => $objectname));
        if (empty($data['object'])) return xarResponse::NotFound();

        // Security
        //if (!$data['object']->checkAccess('update'))
            //return xarResponse::Forbidden(xarML('Clone #(1) is forbidden', $object->label));

        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['authid'] = xarSecGenAuthKey();

        $data['name'] = $data['object']->properties['name']->value;
        $data['title'] = $data['object']->properties['title']->value;

        $data['label'] = $data['object']->label;
        xarTplSetPageTitle(xarML('Clone Publication #(1) in #(2)', $data['itemid'], $data['label']));

        if ($confirm) {
            if (!xarSecConfirmAuthKey()) return;
            // Get the name for the clone
            if(!xarVarFetch('newname',   'str', $newname,   "", XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('newtitle',   'str', $newtitle,   "", XARVAR_NOT_REQUIRED)) {return;}
            if (empty($newname)) $newname = $data['name'] . "_" . time();
            if (empty($newtitle)) $newtitle = $data['title'] . "_" . time();
            if ($newname == $data['name']) $newname = $data['name'] . "_" . time();
            if ($newtitle == $data['title']) $newname = $data['title'] . "_" . time();
            $newname = strtolower(str_ireplace(" ", "_", $newname));
            //$newtitle = strtolower(str_ireplace(" ", "_", $newtitle));

            // Create the clone
            $data['object']->properties['name']->setValue($newname);
            $data['object']->properties['title']->setValue($newtitle);
            $data['object']->properties['id']->setValue(0);
            $cloneid = $data['object']->createItem(array('itemid' => 0));

            //Get categories of the event selected to clone
            sys::import('xaraya.structures.query');
            xarMod::apiLoad('categories');
            $xartable = xarDB::getTables();
            $q = new Query('SELECT', $xartable['categories_linkage']);
            $q->addfield('category_id');
            $q->addfield('basecategory');
            $q->addfield('child_category_id');
            $q->eq('item_id', (int)$data['itemid']);
            $q->eq('module_id', 182);
            $q->eq('itemtype', 29);
            $q->run();
            $result = $q->output();
            // Insert the categories for the event to be cloned
            if(!empty($result)) {
                foreach ($result as $row) {

                    $q1 = new Query('INSERT', $xartable['categories_linkage']); 
                    $q1->addfield('item_id', (int)$cloneid);
                    $q1->addfield('module_id', 182);
                    $q1->addfield('itemtype', 29);
                    $q1->addfield('basecategory', $row['basecategory']);
                    $q1->addfield('category_id', $row['category_id']);
                    $q1->addfield('child_category_id', $row['child_category_id']);
                    $q1->run();
                }
            }

            // Redirect if we came from somewhere else
            //$current_listview = xarSession::getVar('publications_current_listview');
            if (!empty($return_url)) {
                xarController::redirect($return_url);
            } elseif (!empty($current_listview)) {
                xarController::redirect($current_listview);
            } else {
                xarController::redirect(xarModURL('publications', 'user', 'modify', array('itemid' => $cloneid, 'name' => $objectname)));
            }
            return true;
        }
        return $data;
    }
?>