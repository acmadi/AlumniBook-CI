<?php
	/** Trans-Iluni DB @version 0.1.6 @package Trans-Iluni Database **/
	if (!defined('BASEPATH')) exit('No direct script access allowed'); 
?>

<table class="adminlist" cellpadding="1">
<thead>
	<tr>
		<th width="2%" class="title">#</th>
	    <th align="left" nowrap>Organization</th>
		</th>
	</tr>
</thead>	
<tbody>
<?php  foreach($rows as $i=>$row)	:	$id=$row->OID; ?>
	<tr class="row<?=($i%2);?>">
		<td><?=$i+1;?></td>
		<td><a href="javascript:void(0);" onclick="pick(<?=$id;?>);"  title="select">
				<?=$row->ORGANIZATION;?></a>	</td>					
	</tr>
<?php endforeach; ?>	
</tbody>
</table>		