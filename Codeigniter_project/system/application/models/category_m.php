<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// keep a temporary list of products not found
class Category_m extends MY_Model
{
	public $filter = 0;

    function __construct() {
		$this->tableName = "categories"; //TODO: get this from config
    }
	
	// TODO: add this to database driver
    function db_getone($sql, $index){
		$row = $this->db->query($sql)->row_array();
		if(!empty($row[$index])){
			return $row[$index];
		}
	}

    function get_breadcrumb($catId){
        $catId = intval($catId);		
        $sql = "select * from categories where id='$catId'";
        $curr_row = $this->db->query($sql)->row_array();
        $parentid = isset($curr_row['parentid']) ? intval($curr_row['parentid']) : false;
        if(!empty($curr_row)){
			$breadcrumb[] = $curr_row;
        }
        do{
            $curr_row = array();
            if($parentid !== false){
				$sql = "select * from categories where id={$parentid}";
				$curr_row = $this->db->query($sql)->row_array();
        		$parentid = isset($curr_row['parentid']) ? intval($curr_row['parentid']) : false;
            }	
            if(!empty($curr_row)){
				$breadcrumb[] = $curr_row;
            }
            if($parentid === '-1'){
				break;
            }
        }while (!empty($curr_row['parentid']));
        
        //set root category
        $breadcrumb[] = array('id'=>-1, 'name'=>'Home');
       
        return array_reverse($breadcrumb);
    }
    
    //Get all categories. 
    //This is a faster method than get_categories, but it returns only categories, not hierarchy like get_categories function!
    function get_simple_categories($type='all', $parentid=false, $order=false){
        $parentid = intval($parentid);
        $where=$orderby='';
        if($type=='root') $where = 'WHERE parentid = -1';
        elseif ($type=='sub' && $parentid) $where = "WHERE parentid = '$parentid'";
        if($order) $orderby = ' order by name '.$order;
        $sql = "SELECT id as catId, name as catName FROM categories " . $where . $orderby; //, marketplaceCount
        $cats = $this->db->query($sql)->result_array();
        foreach($cats as $key=>$value){
            //$cats[$key]['marketplaceCount'] = number_format($value['marketplaceCount']);
            $haveSubCategories = $this->db_getone("SELECT id from categories where parentid='{$value['catId']}'", 'id');
            if($haveSubCategories) $cats[$key]['haveSubCategories'] = 1;
            else $cats[$key]['haveSubCategories'] = 0;
        }
        return $cats;
    }
		    
    function get_category_name($catId){
        $catId = intval($catId);
        if($catId){
            $name = $this->db_getone("SELECT name FROM categories WHERE id=$catId", 'name');
        }
        else{
            $name='';	
        }
        return $name;	
    }
    
    //return all categories
    function get_category($catId){
        if($catId === false){
            return array(
                'id' => false,
                'parentid' => '-1',
                'name' => '',
                'level' => '',
                'weight' => '',
            );
        }
        $catId = intval($catId);
        if($catId){
			$sql = "SELECT * FROM categories WHERE id=$catId";
            $category = $this->db->query($sql)->row_array();	
        }
        return $category;	
    }
    
    //similiar to get_simple_categories(), but it return complete list of categories, in hierarchy
    function get_categories($params=''){
        $sql = ('SELECT id, name, parentid, level FROM categories ORDER BY name');
        $cat_array = $this->db->query($sql)->result_array();
        
        //this will generate html output - probably faster, but html code is out of templates!!!
        //$output_html = $this->generate_list_html("-1",$cat_array);
        
        //this is slower method, but no html code in php
        //for this option, smarty modifier(compiler) need to be installed(compiler.defun.php)
        $arr = array();
        if(!empty($params['catId']) && $params['catId'] > 0){		
            //generate top category, because it is not back from recursion function 	
            $this->CreateNestedArray($cat_array, $arr, $params['catId'], 0, 20);
            $arr2[0] = $this->get_category($params['catId']);
            $arr2[0]['children'] = $arr;
            return $arr2;
        }else{
            //get all categories
            $this->CreateNestedArray($cat_array, $arr, "-1", 0, 20);
            return $arr;
        }
          
        return;
    }

    function CreateNestedArray(&$data, &$arr, $parent, $startDepth=0, $maxDepth=6)
    {
        if ($maxDepth-- == 0) return;
        $index = 0;
        $startDepth++;
        
        $this->filter = $parent;
        $children = array_filter($data, array($this, "FilterMethod"));
        foreach ($children as $child)
        {
            $arr[$index] = $child;
            $arr[$index]['level'] = $startDepth;
            $this->CreateNestedArray($data, $arr[$index]['children'], $child['id'], $startDepth, $maxDepth);
            $index++;
        }
    }
       
	function FilterMethod($row){
		//you need to replace $row['parentid'] by your name of column, which is holding the parent's id of current entry!
		return $row['parentid'] == $this->filter;
    }
    
    
    function generate_option_html($parent='-1', $cat_array=false)
    {
        static $output_html = "";
        static $output_array = array();
        //use global array variable instead of a local variable to lower stack memory requierment
        if(!$cat_array){
            global $cat_array;
            $cat_array = $this->get_categories();
            $parent = '-1';
        }
        foreach($cat_array as $key => $value)
        {
            $output_html .= "<option value='{$value['id']}'>" . str_repeat('--', $value['level']) . $value['name'] . "</option>\n";
            if ($value['parentid'] == $parent) 
            {       
                $subcat_array = array();
                if(!empty($value['children'])){
                    $subcat_array = $value['children'];
                    //call function again to generate nested list for subcategories belonging to this category
                    $this->generate_option_html($value['id'],$subcat_array);
                }
            }
        }
        return $output_html;
    }

    function generate_list_html($parent='-1', $cat_array = false, $with_checkboxes=false)
    {
        static $output_html = "";
        static $output_array = array();
        //use global array variable instead of a local variable to lower stack memory requierment
        if(!$cat_array){
            global $cat_array;
            $cat_array = $this->get_categories();
            $parent = '-1';
        }
        //this prevents printing 'ul' if we don't have subcategories for this category            
        $has_children = false;
        $output_html .= "<ul>\n";
        foreach($cat_array as $key => $value)
        {
            $output_html .= '<li>';
			$output_html .= ($with_checkboxes)? '<input class="checkbox1" type="checkbox" name="catIds[]" value="'.$value['id'].'"> ' : '';
			$output_html .= '<a href="/category/show/catId=' . $value['id'] . '">' . $value['name'] . "</a></li>\n";
            if ($value['parentid'] == $parent) 
            {       
                $new_array = array();
                if(!empty($value['children'])){
                    $has_children = true;
                    $new_array = $value['children'];
                    //call function again to generate nested list for subcategories belonging to this category
                    $this->generate_list_html($value['id'],$new_array, $with_checkboxes);
                }
            }
        }
        $output_html .= "</ul>\n";
        return $output_html;
    }


  
    //admin functions
	function save( $params ){
		isset($params['catId']) ? $catId = intval($params['catId']) : $catId=false;
		isset($params['parentid']) ? $parentid = intval($params['parentid']) : $parentid ='-1';
 		isset($params['name']) ? $name = addslashes($params['name']) : $name ='-1';
 		
		$errors = array();
		if(!$name)	return array('errors' =>  'Category Name was missing');
		
		if(!$catId){
			$level = $this->get_level_from_parentid($parentid);
			$sql = "INSERT INTO categories (name, parentid, level) VALUES('$name', '$parentid', '$level')";
		}else{
			$sql = "UPDATE categories SET name='$name' WHERE id=$catId";
		}
		$res = $this->db->simple_query($sql);
		return $res;
	}
	
	function get_level_from_parentid($parentid = '-1'){
		if($parentid=='-1'){
			return '0';
		}
		$sql = "SELECT level FROM categories WHERE id='{$parentid}'";
		return $this->db_getone($sql, 'level');
	}
	
	//move all data related to this category under another category
	function move_data( $params ){
		isset($params['srcCatId']) ? $srcCatId = intval($params['srcCatId']) : $srcCatId=false;
		isset($params['dstCatId']) ? $dstCatId = intval($params['dstCatId']) : $dstCatId=false;
			
		//if we have src and destination category, move data
		if($srcCatId && $dstCatId && $srcCatId!=$dstCatId){
			$heirarchy_ok = $this->test_heirarchy_relation($srcCatId, $dstCatId);
			if($heirarchy_ok){
				//change parent category, if we have subcategories
				$sql = "UPDATE categories SET parentid='$dstCatId' WHERE id='$srcCatId'";
				$this->db->simple_query($sql);
				//update marketplaces -- TODO: should we move it up or leave it be?
				//$this->db->simple_query("UPDATE categories_marketplaces SET cId='$dstCatId' WHERE cId='$srcCatId'");
			}
			else{
				return array(
					'errors'=>
					'Don\'t try to move a category deeper into it\'s own branch!  First move the sub-branches'
				);
			}
		}
		else{
			return array(
				'errors'=>
				'Something went wrong.  Check that you selected a destination category, and that it is different from the source category'
			);
		}
	}
	
	function test_heirarchy_relation($srcCatId, $dstCatId){
		$parentid = $dstCatId;
        do{
            if($parentid !== false){
				$sql = "select parentid from categories where id={$parentid}";
				$parentid = $this->db_getone($sql, 'parentid');
            }	
            if($parentid==$srcCatId){
				return false;
            }
            if($parentid === '-1'){
				break;
            }
        }while ($parentid);
		
		return true;
	}
	
	function delete($catId){
		$catId=intval($catId);
		$this->db->simple_query("DELETE FROM categories WHERE id='$catId'");
		$this->db->simple_query("DELETE FROM categories_marketplaces WHERE cId='$catId'");		
	}
	
	function get_marketplace_ids_by_category_id($catId){
        $cat_array = $this->get_marketplaces_by_category_id($catId);
		$out = array();
		foreach($cat_array as $cm){
			$out[] = $cm['mId'];
		}
		return $out;
	}
	
	function get_marketplaces_by_category_id($catId){
        $sql = ('SELECT mId FROM categories_marketplaces where cId = '.$catId);
        return $this->db->query($sql)->result_array();
	}
	
	function get_category_ids_by_storeId($storeId){
        $cat_array = $this->get_categories_by_storeId($storeId);
		$out = $delim = "";
		foreach($cat_array as $cs){
			$out .= "{$delim}'{$cs['cId']}'";
			$delim = ",";
		}
		return $out;
	}
	
	function get_categories_by_storeId($storeId){
        $sql = 'SELECT cId FROM categories_stores where sId = '.$storeId;
        return $this->db->query($sql)->result_array();
	}
	
	function get_stores_by_api($api, $store_id=false){
        $sql = "
			SELECT s.id FROM 
				marketplaces m,
				categories_marketplaces cm,
				categories_stores cs,
				store s
			WHERE
				m.name = '{$api}'
				AND m.id = cm.mId
				AND cm.cId = cs.cId
				AND cs.sId = s.id
		";
		if($store_id){
			$sql .= "AND s.id={$store_id}";
		}
        return $this->db->query($sql)->result_object();
	}
	
	function save_categories_marketplaces($catId, $marketplaceIds){
		$this->db->simple_query("DELETE FROM categories_marketplaces WHERE cId='$catId'");
		if(empty($marketplaceIds)) return true;
		foreach($marketplaceIds as $marketplaceId){
			$sql = "INSERT INTO categories_marketplaces SET cId={$catId}, mId={$marketplaceId}";
			$this->db->query($sql);
		}
	}
	
	function save_categories_stores($storeId, $catIds){
		$this->db->simple_query("DELETE FROM categories_stores WHERE sId='$storeId'");
		if(empty($catIds)) return true;
		foreach($catIds as $catId){
			$sql = "INSERT INTO categories_stores SET cId={$catId}, sId={$storeId}";
			$this->db->query($sql);
		}
	}
}

/**
--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `marketplaceCount` int(11) default NULL,
  `parentid` int(11) NOT NULL default '-1',
  `level` tinyint(2) NOT NULL default '0',
  `weight` float NOT NULL default '1',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=latin1 COMMENT='';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `categories`
--
*/
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*
/****************************
INSERT INTO `categories` VALUES (1,'Appliances',NULL,NULL,-1,0,1),(2,'Apps & Games',NULL,NULL,-1,0,1),(3,'Arts, Crafts & Sewing',NULL,NULL,-1,0,1),(4,'Automotive',NULL,NULL,-1,0,1),(5,'Baby',NULL,NULL,10,0,1),(6,'Beauty',NULL,NULL,-1,0,1),(7,'Books',NULL,NULL,-1,0,1),(8,'CDs & Vinyl',NULL,NULL,-1,0,1),(9,'Cell Phones & Accessories',NULL,NULL,-1,0,1),(10,'Clothing, Shoes & Jewelry',NULL,NULL,-1,0,1),(11,'Women',NULL,NULL,10,0,1),(12,'Men',NULL,NULL,10,0,1),(13,'Girls',NULL,NULL,10,0,1),(14,'Boys',NULL,NULL,10,0,1),(15,'Baby',NULL,NULL,-1,0,1),(16,'Collectibles & Fine Art',NULL,NULL,-1,0,1),(17,'Computers',NULL,NULL,-1,0,1),(18,'Credit and Payment Cards',NULL,NULL,-1,0,1),(19,'Digital Music',NULL,NULL,-1,0,1),(20,'Electronics',NULL,NULL,-1,0,1),(21,'Gift Cards',NULL,NULL,-1,0,1),(22,'Grocery & Gourmet Food',NULL,NULL,-1,0,1),(23,'Health & Personal Care',NULL,NULL,-1,0,1),(24,'Home & Kitchen',NULL,NULL,-1,0,1),(25,'Industrial & Scientific',NULL,NULL,-1,0,1),(26,'Kindle Store',NULL,NULL,-1,0,1),(27,'Luggage & Travel Gear',NULL,NULL,-1,0,1),(28,'Magazine Subscriptions',NULL,NULL,-1,0,1),(29,'Movies & TV',NULL,NULL,-1,0,1),(30,'Musical Instruments',NULL,NULL,-1,0,1),(31,'Office Products',NULL,NULL,-1,0,1),(32,'Patio, Lawn & Garden',NULL,NULL,-1,0,1),(33,'Pet Supplies',NULL,NULL,-1,0,1),(34,'Prime Pantry',NULL,NULL,-1,0,1),(35,'Software',NULL,NULL,-1,0,1),(36,'Sports & Outdoors',NULL,NULL,-1,0,1),(37,'Tools & Home Improvement',NULL,NULL,-1,0,1),(38,'Toys & Games',NULL,NULL,-1,0,1),(39,'Video Games',NULL,NULL,-1,0,1),(40,'Wine',NULL,NULL,-1,0,1);
*/
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

/*
--
-- Table structure for table `categories_marketplaces`
--
 
DROP TABLE IF EXISTS `categories_marketplaces`;
CREATE TABLE `categories_marketplaces` (
  `mId` int(10) default NULL,
  `cId` int(10) default NULL,
  UNIQUE KEY `pId` (`pId`,`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='each marketplace can be in more than one category'
 

--
-- Table structure for table `categories_stores`
--
 
DROP TABLE IF EXISTS `categories_stores`;
CREATE TABLE `categories_stores` (
  `sId` int(10) default NULL,
  `cId` int(10) default NULL,
  UNIQUE KEY `sId` (`sId`,`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='each store can be in more than one category'
 

*/
?>
