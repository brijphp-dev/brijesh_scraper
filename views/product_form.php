<?php

?>

<form action="http://localhost/brij_test/product/addProduct" method="post" enctype="multipart/form-data" name="prod_insert">
	<table>
		<tr>
			<td>Product Name:</td>
			<td><input type="text" name="prod_name" id="prod_name"></td>
		</tr>
		<tr class="vars_more">
			<td>Product Variants:</td>
			<td>
				<div class="more_vars_table">
					<table>
						<tr>
							<td>Var Name:</td>
							<td><input type="text" name="prod_vars[]" id="prod_vars"></td>
						</tr>
						<tr>
							<td>Var Desc:</td>
							<td><input type="text" name="prod_vars_desc[]" id="prod_vars_desc"></td>
						</tr>
					</table>
				</div>
				<a href="javascript:void(0);" id="add_more_vars">Add More</a>
			</td>
		</tr>
		<tr>
			<td>Product Image:</td>
			<td><input type="file" name="prod_image[]" multiple=""></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="prod_submit" value="Submit">
			</td>
		</tr>
	</table>
</form>

<div>
	<?php
		$show_product = new product();
		$all_prod = $show_product->show_product();
		echo '<table border="1">';
		if(sizeof($all_prod) > 0){
			foreach($all_prod as $product){
		?>
				<tr>
					<td><?php echo $product['pid']; ?></td>
					<td><?php echo $product['pname']; ?></td>
					<td>
						<table border="1">
						<?php
							$mult_vars = explode('#@', $product['vars']);
							$mult_vars_desc = explode('#@', $product['vars_desc']);
							echo '<tr>';
							echo '<th>Vars</th>';
							echo '<th>Vars Desc</th>';
							echo '</tr>';
							for ($i=0; $i < sizeof($mult_vars); $i++){
								echo '<tr>';
								echo '<td>' . $mult_vars[$i]  . '</td>';
								echo '<td>' . $mult_vars_desc[$i] . '</td>';
								echo '</tr>';
							}
						?>
						</table>
					</td>
					<td>
						
						<table>
						<?php
							$mult_imgs = explode('#@', $product['imgs']);
							for ($i=0; $i < sizeof($mult_imgs); $i++){
								echo '<tr>';
								echo '<td> <img src="upld/' . $mult_imgs[$i]  . '" style="width: 150px;"></td>';
								echo '</tr>';
							}
						?>
						</table>
					</td>
				</tr>
		<?php
			}
		}
echo '</table>';
	?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	$(".vars_more #add_more_vars").on('click', function(){
		var prod_vars_append = '<tr><td>Var Name:</td><td><input type="text" name="prod_vars[]" id="prod_vars"></td></tr><tr><td>Var Desc:</td><td><input type="text" name="prod_vars_desc[]" id="prod_vars_desc"></td></tr>';
		$(prod_vars_append).clone().insertAfter(".more_vars_table tr:last");
	});
</script>