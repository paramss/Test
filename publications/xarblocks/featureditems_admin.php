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
 * modify block settings
 * @author Jonn Beames et al
 */

    sys::import('modules.publications.xarblocks.featureditems');

    class Publications_FeatureditemsBlockAdmin extends Publications_FeatureditemsBlock
    {
        function modify()
        {
            $data = $this->getContent();
        
            $data['fields'] = array('id', 'name');

            if (!is_array($data['pubstate'])) {
                $statearray = array($data['pubstate']);
            } else {
                $statearray = $data['pubstate'];
            }
        
            if(!empty($data['catfilter'])) {
                $cidsarray = array($data['catfilter']);
            } else {
                $cidsarray = array();
            }

# ------------------------------------------------------------
# Set up the different conditions for getting the items that can be featured
#
            $conditions = array();

            // Only include pubtype if a specific pubtype is selected
            if (!empty($data['pubtype_id'])) $conditions['ptid'] = $data['pubtype_id'];

            // If itemlimit is set to 0, then don't pass to getall
            if ($data['itemlimit'] != 0 ) {
                $conditions['numitems'] = $data['itemlimit'];
            }
        
            // Add the rest of the arguments
            $conditions['cids'] = $cidsarray;
            $conditions['enddate'] = time();
            $conditions['state'] = $statearray;
            $conditions['fields'] = $data['fields'];
            $conditions['sort'] = $data['toptype'];

# ------------------------------------------------------------
# Get the items for the dropdown based on the conditions
#
            $items = xarMod::apiFunc('publications', 'user', 'getall', $conditions );

            // Limit the titles to less than 50 characters
            $data['filtereditems'] = array();
            foreach ($items as $key => $value) {
                if (strlen($value['title']) > 50) $value['title'] = substr($value['title'], 0, 47) . '...';
                $value['original_name'] = $value['name'];
                $value['name'] = $value['title'];
                $data['filtereditems'][$value['id']] = $value;
            }

            // Remove the featured item and reuse the items for the additional headlines multiselect
            $data['morepublications'] = $data['filtereditems'];
            unset($data['morepublications'][$this->featuredid]);

# ------------------------------------------------------------
# Get the data for other dropdowns
#
            $data['pubtypes'] = xarMod::apiFunc('publications', 'user', 'get_pubtypes');
            $data['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');
            $data['sortoptions'] = array(
                array('id' => 'author', 'name' => xarML('Author')),
                array('id' => 'date', 'name' => xarML('Date')),
                array('id' => 'hits', 'name' => xarML('Hit Count')),
                array('id' => 'rating', 'name' => xarML('Rating')),
                array('id' => 'title', 'name' => xarML('Title'))
            );
        
            return $data;
        }

        function update(Array $data=array())
        {
            $args = array();
            xarVarFetch('pubtype_id',       'int',       $args['pubtype_id'],      0, XARVAR_NOT_REQUIRED);
            xarVarFetch('catfilter',        'id',        $args['catfilter'],       $this->catfilter, XARVAR_NOT_REQUIRED);
            xarVarFetch('nocatlimit',       'checkbox',  $args['nocatlimit'],      $this->nocatlimit, XARVAR_NOT_REQUIRED);
            xarVarFetch('pubstate',         'str',       $args['pubstate'],        $this->pubstate, XARVAR_NOT_REQUIRED);
            xarVarFetch('itemlimit',        'int:1',     $args['itemlimit'],       $this->itemlimit, XARVAR_NOT_REQUIRED);
            xarVarFetch('toptype',  'enum:author:date:hits:rating:title', $args['toptype'], $this->toptype, XARVAR_NOT_REQUIRED);
            xarVarFetch('featuredid',       'int',        $args['featuredid'],      $this->featuredid, XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle',         'str',       $args['alttitle'],        $this->alttitle, XARVAR_NOT_REQUIRED);
            xarVarFetch('altsummary',       'str',       $args['altsummary'],      $this->altsummary, XARVAR_NOT_REQUIRED);
            xarVarFetch('moreitems',        'list:id',   $args['moreitems'],       array(), XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedbod',  'checkbox',  $args['showfeaturedbod'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedsum',  'checkbox',  $args['showfeaturedsum'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsummary',      'checkbox',  $args['showsummary'],     0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showvalue',        'checkbox',  $args['showvalue'],       0, XARVAR_NOT_REQUIRED);
            xarVarFetch('linkpubtype',      'checkbox',  $args['linkpubtype'],     0, XARVAR_NOT_REQUIRED);
            xarVarFetch('linkcat',          'checkbox',  $args['linkcat'],         0, XARVAR_NOT_REQUIRED);

            $this->setContent($args);
            return true;        
        }
}
?>