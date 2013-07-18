<?php
/**
 * Action for bulk operations
 */
sys::import('modules.dynamicdata.class.objects.master');
function publications_admin_multiops()
{
    // Get parameters
    if(!xarVarFetch('idlist',   'isset', $idlist,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('operation',   'isset', $operation,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('redirecttarget',   'isset', $redirecttarget,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('returnurl',   'str', $returnurl,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('objectname',   'str', $objectname,  'listings_listing', XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('localmodule',   'str', $module,  'listings', XARVAR_DONT_SET)) {return;}

    // Confirm authorisation code
    //if (!xarSecConfirmAuthKey()) return;

    // Catch missing params here, rather than below
    if (empty($idlist)) {
        return xarTplModule('publications','user','errors',array('layout' => 'no_items'));
    }
    if ($operation === '') {
        return xarTplModule('publications','user','errors',array('layout' => 'no_operation'));
    }

    $ids = explode(',',$idlist);

    switch ($operation) {
        case 0:
        foreach ($ids as $id => $val) {
            if (empty($val)) continue;

            // Get the item
             $item = $object->getItem(array('itemid' => $val));
            
            // Update it
             if (!$object->deleteItem(array('state' => $operation))) return;
        }
        break;

    }
    return true;
}

?>