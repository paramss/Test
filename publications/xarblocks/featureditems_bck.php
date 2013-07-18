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
 * Featured initialise block
 *
 * @author Jonn Beams (based on code from TopItems block)
 *
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_FeatureditemsBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'featureditems';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Featured Items';  // Block type display name
    protected $text_type_long   = 'Show featured publications'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared 
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $numitems            = 5;
    public $pubtype_id          = 0;
    public $linkpubtype         = true;
    public $itemlimit           = 0;
    public $featuredid          = 0;
    public $catfilter           = 0;
    public $linkcat             = false;
    public $includechildren     = false;
    public $nocatlimit          = true;
    public $alttitle            = '';
    public $altsummary          = '';
    public $showvalue           = true;
    public $moreitems           = array();
    public $showfeaturedsum     = false;
    public $showfeaturedbod     = false;
    public $showsummary         = false;
    // chris: state is a reserved property name used by blocks
    //public $state               = '2,3';
    public $pubstate            = '2,3';
    public $toptype             = 'ratings'; 

    public function display()
    {
        $data = $this->getContent();
        
        // defaults
        $featuredid = $data['featuredid'];
        
        $fields = array('id', 'title', 'cids');
        
        $fields[] = 'dynamicdata';
        
        // Initialize arrays
        $data['feature'] = array();
        $data['items'] = array();

        // Setup featured item
        if ($featuredid > 0) {
        
            if (xarModIsHooked('uploads', 'publications', $data['pubtype_id'])) {
                xarVarSetCached('Hooks.uploads','ishooked',1);
            }
        
            if ($featart = xarModAPIFunc('publications','user','getall',
                array(
                    'ids' => array($featuredid),
                    'extra' => array('cids','dynamicdata')))
                ) {

                foreach($featart as $featuredart) {

                    $fieldlist = array('id', 'title', 'summary', 'owner', 'pubdate',
                                   'pubtype_id', 'notes', 'state', 'body', 'cids');
            
                    $featuredlink = xarModURL(
                        'publications', 'user', 'display',
                        array(
                            'itemid' => $featuredart['id'],
                            'itemtype' => (!empty($data['linkpubtype']) ? $featuredart['pubtype_id'] : NULL),
                            'catid' => ((!empty($data['linkcat']) && !empty($data['catfilter'])) ? $data['catfilter'] : NULL)
                        )
                    );
                    if (empty($data['showfeaturedbod'])) {$data['showfeaturedbod'] = false;}
                    if(!isset($featuredart['cids'])) $featuredart['cids'] = "";

                    $feature= array(
                        'featuredname'      => $featuredart['name'],
                        'featuredlabel'     => $featuredart['title'],
                        'featuredlink'      => $featuredlink,
                        'alttitle'          => $data['alttitle'],
                        'altsummary'        => $data['altsummary'],
                        'showfeaturedsum'   => $data['showfeaturedsum'],
                        'showfeaturedbod'   => $data['showfeaturedbod'],
                        'featureddesc'      => $featuredart['summary'],
//                        'featuredbody'      => $featuredart['body'],
                        'featuredcids'      => $featuredart['cids'],
                        'pubtype_id'        => $featuredart['pubtype_id'],
                        'featuredid'        => $featuredart['id'],
                        'featureddate'      => $featuredart['start_date']
                    );
        
                    // Get rid of the default fields so all we have left are the DD ones
                    foreach ($fieldlist as $field) {
                        if (isset($featuredart[$field])) {
                            unset($featuredart[$field]);
                        }
                    }
        
                    // now add the DD fields to the featuredart
                    $feature = array_merge($featuredart, $feature);
                    $data['feature'][] = $feature;
                }
            }

            // Setup additional items
            $fields = array('id', 'title', 'pubtype_id', 'cids');
        
            // Added the 'summary' field to the field list.
            if (!empty($data['showsummary'])) {
                $fields[] = 'summary';
            }
        
            if ($data['toptype'] == 'rating') {
                $fields[] = 'rating';
                $sort = 'rating';
            } elseif ($data['toptype'] == 'hits') {
                $fields[] = 'counter';
                $sort = 'hits';
            } elseif ($data['toptype'] == 'date') {
                $fields[] = 'pubdate';
                $sort = 'date';
            } else {
               $sort = $data['toptype'];
            }

            if (!empty($data['moreitems'])) {
                $publications = xarModAPIFunc(
                    'publications', 'user', 'getall',
                    array(
                        'ids' => $data['moreitems'],
                        'enddate' => time(),
                        'fields' => $fields,
                        'sort' => $sort
                    )
                );
        
                // See if we're currently displaying an article
                if (xarVarIsCached('Blocks.publications', 'id')) {
                    $curid = xarVarGetCached('Blocks.publications', 'id');
                } else {
                    $curid = -1;
                }
        
                foreach ($publications as $article) {
                    if ($article['id'] != $curid) {
                        $link = xarModURL(
                            'publications', 'user', 'display',
                            array (
                                'itemid' => $article['id'],
                                'itemtype' => (!empty($vars['linkpubtype']) ? $article['pubtype_id'] : NULL),
                                'catid' => ((!empty($data['linkcat']) && !empty($data['catfilter'])) ? $data['catfilter'] : NULL)
                            )
                        );
                    } else {
                        $link = '';
                    }
        
                    $count = '';
                    // TODO: find a nice clean way to show all sort types
                    if ($data['showvalue']) {
                        if ($data['toptype'] == 'rating') {
                            $count = intval($article['rating']);
                        } elseif ($data['toptype'] == 'hits') {
                            $count = $article['counter'];
                        } elseif ($data['toptype'] == 'date') {
                            // TODO: make user-dependent
                            if (!empty($article['pubdate'])) {
                                $count = strftime("%Y-%m-%d", $article['pubdate']);
                            } else {
                                $count = 0;
                            }
                        } else {
                            $count = 0;
                        }
                    } else {
                        $count = 0;
                    }
                    if (isset($article['cids'])) {
                       $cids=$article['cids'];
                    }else{
                       $cids='';
                    }
                    if (isset($article['pubdate'])) {
                       $pubdate=$article['pubdate'];
                    }else{
                       $pubdate='';
                    }
                    // Pass $desc to items[] array so that the block template can render it
                    $data['items'][] = array(
                        'id' => $article['id'],
                        'label' => $article['title'],
                        'link' => $link,
                        'count' => $count,
                        'cids' => $cids,
                        'pubdate' => $pubdate,
                        'desc' => ((!empty($data['showsummary']) && !empty($article['summary'])) ? $article['summary'] : ''),
                    );
                }
            }}
            if (empty($data['feature']) && empty($data['items'])) {
                // Nothing to display.
                return;
            }
            return $data;
        }
    }

?>