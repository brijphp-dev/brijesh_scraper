<?php

class product extends db{
	
	public function index(){
		include_once "views/product_form.php";
	}

	public function show_product(){
		$get_all_Product = "select P.pid, P.pname, GROUP_CONCAT(DISTINCT PV.pvars_name ORDER by PV.pvid SEPARATOR '#@') as vars, GROUP_CONCAT(DISTINCT PV.pvars_desc ORDER by PV.pvid SEPARATOR '#@') vars_desc, GROUP_CONCAT(DISTINCT PI.pimage ORDER by PI.piid SEPARATOR '#@') as imgs from tbl_products as P inner join tbl_products_vars as PV on (P.pid = PV.pid) inner Join tbl_products_image as PI on(P.pid = PI.pid) GROUP BY P.pid ORDER BY P.pid";

		$result = mysqli_query($this->conn, $get_all_Product);
		$all_product = array();
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
			   $all_product[] =  $row;
			}
		}
		return $all_product;

	}

	public function addProduct(){
		$prod_name = mysqli_real_escape_string($this->conn, $_POST['prod_name']);

		$insert_product = "insert into tbl_products(pname) values ('".$prod_name."')";
		$last_inserted_Product = '';
		if (mysqli_query($this->conn, $insert_product)) {
			$last_inserted_Product = mysqli_insert_id($this->conn);
		}
		else{
			return 0;
		}
		if($last_inserted_Product != ''){
			// Insert product vars
			foreach($_POST['prod_vars'] as $key => $prod_vars){
				$prod_vars = mysqli_real_escape_string($this->conn, $prod_vars);
				$prod_desc = mysqli_real_escape_string($this->conn, $_POST['prod_vars_desc'][$key]);
				$insert_product_vars = "insert into tbl_products_vars(pid, pvars_name, pvars_desc) values ('".$last_inserted_Product."', '".$prod_vars."', '".$prod_desc."')";
				mysqli_query($this->conn, $insert_product_vars);
			}

			// insert Images
			$accepted_image_ext = array("png", "jpg", "jpeg");
			if(count($_FILES['prod_image']['name']) > 0){
	    
		        for($i=0; $i < count($_FILES['prod_image']['name']); $i++) {
		        	if($_FILES['prod_image']['error'][$i] == 0){
		        	
		        		$file_extension = pathinfo($_FILES["prod_image"]["name"][$i], PATHINFO_EXTENSION);
		        		if(in_array($file_extension, $accepted_image_ext)){
		        			
		        			if (($_FILES["prod_image"]["size"][$i] <= 2097152)){
		        				$newfileName = time(). $_FILES['prod_image']['name'][$i];				                

				                if(move_uploaded_file($_FILES['prod_image']['tmp_name'][$i], "upld/" . $newfileName)) {
				                    $insert_product_img = "insert into tbl_products_image(pid, pimage) values ('".$last_inserted_Product."', '".$newfileName."')";
									mysqli_query($this->conn, $insert_product_img);
				                }
		        			}else{
		        				header('Location: http://localhost/brij_test/product');
		        			}
		        		}else{
		        			header('Location: http://localhost/brij_test/product');
		        		}
		        	}
		        }
			}
		}
		header('Location: http://localhost/brij_test/product');
	}

	function __destruct(){
		mysqli_close($this->conn);
	}
}