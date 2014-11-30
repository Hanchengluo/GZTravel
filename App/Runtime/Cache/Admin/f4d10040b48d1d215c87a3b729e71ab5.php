<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="__URL__/index" method="post">
	<input type="hidden" name="pageNum" value="<?php echo (($currentPage)?($currentPage):'1'); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" /><!--ÿҳ��ʾ������-->
	<input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ($_REQUEST['_sort']); ?>"/>
</form>

<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="__URL__/add" target="dialog" width="550" height="380" rel="user_msg" title="添加用户"><span>添加</span></a></li>
			<li><a class="delete" href="__URL__/delete/id/{item_id}/navTabId/admin" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<!--<li><a class="edit" href="__URL__/edit/id/{item_id}"  width="550" height="380" target="dialog"><span>修改信息</span></a></li>-->	
			<li><a class="edit" href="__URL__/editpwd/id/{item_id}"  width="550" height="380" target="dialog"><span>修改密码</span></a></li>	
			<li class="line">line</li>
			<li><a class="icon"  href="javascript:navTabPageBreak();"><span>刷新</span></a></li>	
		</ul>
	</div>
	<table class="table" width="100%" layoutH="75">
		<thead>
			<tr>
				<th width="10">ID</th>
				<th width="30">用户名</th>
				<th width="40">上次登陆时间</th>
				<th width="40">所属组别</th>
				<th width="40">状态</th>
				<th width="40">操作</th>	
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr target="item_id" rel="<?php echo ($vo["id"]); ?>" <?php if($vo['username']==C('RBAC_SUPERADMIN')): ?>style=""<?php endif; ?> >
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["username"]); ?></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo["logintime"])); ?></td>
					<td>
						<?php if($vo['username']==C('RBAC_SUPERADMIN')): ?><b>超级管理员</b>
						<?php else: ?>
						<b>普通管理员</b><?php endif; ?>
					</td>
					<td><?php echo (islock($vo["islock"])); ?></td>
					<td>

<script>
function updateLock(id,num){
	$.post('__URL__/update/navTabId/admin/callbackType/closeCurrent',{'id':id,'islock':num},function(data){
		if (data) {
			navTabPageBreak();
		};	
	});	
}
</script>
						<?php if($vo["islock"] == 1): ?><a href="javascript:void(0);" onclick="updateLock(<?php echo ($vo["id"]); ?>,0);">解锁</a>
						<?php else: ?>
							<?php if($vo['username']==admin): ?>无法锁定<?php else: ?>
								<a href="javascript:void(0);" onclick="updateLock(<?php echo ($vo["id"]); ?>,1);">锁定</a><?php endif; endif; ?>
					</td>
					
				</tr><?php endforeach; endif; ?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="10" <?php if($numPerPage == 10 ): ?>selected<?php endif; ?>>10</option>
				<option value="15" <?php if($numPerPage == 15 ): ?>selected<?php endif; ?>>15</option>
				<option value="20" <?php if($numPerPage == 20 ): ?>selected<?php endif; ?>>20</option>
				<option value="25" <?php if($numPerPage == 25 ): ?>selected<?php endif; ?>>25</option>
				<option value="30" <?php if($numPerPage == 30 ): ?>selected<?php endif; ?>>30</option>
			</select>
			<span>共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
	</div>
</div>