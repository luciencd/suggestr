<html><body>

<div>
<span>
<?php
ini_set('display_errors', 1);


require_once('suggestions.php');

echo "test";
echo "sum1";	
$Data = new Database();//Find a way to make this local to suggestr.php or something.
echo "<h4>sum2</h4>";		
$Data->updateMajorRelations();


?>
</span>
</div>
</body><html>