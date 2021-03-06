<?php
/**
 * The story view file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: story.html.php 5116 2013-07-12 06:37:48Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $recTotalLabel = " <span class='label label-light label-badge'>{$pager->recTotal}</span>";
    echo html::a(inlink('story', "type=assignedTo"),  "<span class='text'>{$lang->my->storyMenu->assignedToMe}</span>" . ($type == 'assignedTo' ? $recTotalLabel : ''), '', "class='btn btn-link" . ($type == 'assignedTo' ? ' btn-active-text' : '') . "'");
    echo html::a(inlink('story', "type=openedBy"),    "<span class='text'>{$lang->my->storyMenu->openedByMe}</span>"   . ($type == 'openedBy'   ? $recTotalLabel : ''),   '', "class='btn btn-link" . ($type == 'openedBy'   ? ' btn-active-text' : '') . "'");
    echo html::a(inlink('story', "type=reviewedBy"),  "<span class='text'>{$lang->my->storyMenu->reviewedByMe}</span>" . ($type == 'reviewedBy' ? $recTotalLabel : ''), '', "class='btn btn-link" . ($type == 'reviewedBy' ? ' btn-active-text' : '') . "'");
    echo html::a(inlink('story', "type=closedBy"),    "<span class='text'>{$lang->my->storyMenu->closedByMe}</span>"   . ($type == 'closedBy'   ? $recTotalLabel : ''),   '', "class='btn btn-link" . ($type == 'closedBy'   ? ' btn-active-text' : '') . "'");
    ?>
  </div>
</div>
<div id="mainContent">
  <?php if(!$stories):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->story->noStory;?></span></p>
  </div>
  <?php else:?>
  <form id='myStoryForm' class="main-table table-story" data-ride="table" method="post">
    <table id='storyList' class="table has-sort-head table-fixed">
      <?php $vars = "type=$type&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
      <?php
      $canBatchEdit     = common::hasPriv('story', 'batchEdit');
      $canBatchClose    = (common::hasPriv('story', 'batchClose') and strtolower($type) != 'closedby');
      $canBatchReview   = common::hasPriv('story', 'batchReview');
      $canBatchAssignTo = common::hasPriv('story', 'batchAssignTo');

      $canBatchAction   = ($canBatchEdit or $canBatchClose or $canBatchReview or $canBatchAssignTo);
      ?>
      <thead>
        <tr>
          <th class="c-id">
            <?php if($canBatchAction):?>
            <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
              <label></label>
            </div>
            <?php endif;?>
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th class='c-pri w-40px'>      <?php common::printOrderLink('pri',          $orderBy, $vars, $lang->priAB);?></th>
          <th class='c-product'>  <?php common::printOrderLink('program',      $orderBy, $vars, $lang->story->program);?></th>
          <th class='c-product'>  <?php common::printOrderLink('productTitle', $orderBy, $vars, $lang->story->product);?></th>
          <th class='c-name'>     <?php common::printOrderLink('title',        $orderBy, $vars, $lang->story->title);?></th>
          <th class='c-plan'>     <?php common::printOrderLink('plan',         $orderBy, $vars, $lang->story->plan);?></th>
          <th class='c-user'>     <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
          <th class='c-hours'>    <?php common::printOrderLink('estimate',     $orderBy, $vars, $lang->story->estimateAB);?></th>
          <th class='c-status'>   <?php common::printOrderLink('status',       $orderBy, $vars, $lang->statusAB);?></th>
          <th class='c-stage'>    <?php common::printOrderLink('stage',        $orderBy, $vars, $lang->story->stageAB);?></th>
          <th class='c-actions-5'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($stories as $story):?>
        <?php $storyLink = $this->createLink('story', 'view', "id=$story->id", '', '', $story->program);?>
        <tr>
          <td class="c-id">
            <?php if($canBatchAction):?>
            <div class="checkbox-primary">
              <input type='checkbox' name='storyIdList[<?php echo $story->id;?>]' value='<?php echo $story->id;?>' />
              <label></label>
            </div>
            <?php endif;?>
            <?php printf('%03d', $story->id);?>
          </td>
          <td class='c-pri'><span class='label-pri <?php echo 'label-pri-' . $story->pri;?>' title='<?php echo zget($lang->story->priList, $story->pri, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
          <td class='c-product'><?php echo zget($programs, $story->program, '');?></td>
          <td class='c-product'><?php echo $story->productTitle;?></td>
          <td class='c-name nobr <?php if(!empty($story->children)) echo "has-child" ?>'>
            <?php echo html::a($storyLink, $story->title, null, "style='color: $story->color'");?>
            <?php if(!empty($story->children)) echo '<a class="story-toggle" data-id="' . $story->id . '"><i class="icon icon-angle-double-right"></i></a>';;?>
          </td>
          <td class='c-plan'><?php echo $story->planTitle;?></td>
          <td class='c-user'><?php echo zget($users, $story->openedBy);?></td>
          <td class='c-hours'><?php echo $story->estimate;?></td>
          <td class='c-status'><span class='status-story status-<?php echo $story->status;?>'> <?php echo $this->processStatus('story', $story);?></span></td>
          <td class='c-stage'><?php echo zget($lang->story->stageList, $story->stage);?></td>
          <td class='c-actions'>
            <?php
            $vars = "story={$story->id}";
            common::printIcon('story', 'change',     $vars, $story, 'list', 'fork', '', '', '', '', '', $story->program);
            common::printIcon('story', 'review',     $vars, $story, 'list', 'glasses', '', '', '', '', '', $story->program);
            common::printIcon('story', 'close',      $vars, $story, 'list', '', '', 'iframe', true, '', '', $story->program);
            common::printIcon('story', 'edit',       $vars, $story, 'list', '', '', '', '', '', '', $story->program);
            common::printIcon('story', 'createCase', "productID=$story->product&branch=$story->branch&module=0&from=&param=0&$vars", $story, 'list', 'sitemap', '', '', '', '', '', $story->program);
            ?>
          </td>
        </tr>
        <?php if(!empty($story->children)):?>
          <?php $i = 0;?>
          <?php foreach($story->children as $key => $child):?>
          <?php $storyLink = $this->createLink('story', 'view', "id=$child->id", '', '', $child->program);?>
          <?php $class  = $i == 0 ? ' table-child-top' : '';?>
          <?php $class .= ($i + 1 == count($story->children)) ? ' table-child-bottom' : '';?>
          <tr class='table-children<?php echo $class;?> parent-<?php echo $story->id;?>' data-id='<?php echo $child->id?>' data-status='<?php echo $child->status?>' data-estimate='<?php echo $child->estimate?>'>
            <td class="c-id">
              <?php if($canBatchAction):?>
              <div class="checkbox-primary">
                <input type='checkbox' name='storyIdList[<?php echo $child->id;?>]' value='<?php echo $child->id;?>' />
                <label></label>
              </div>
              <?php endif;?>
              <?php printf('%03d', $child->id);?>
            </td>
            <td class='c-pri'><span class='label-pri <?php echo 'label-pri-' . $child->pri;?>' title='<?php echo zget($lang->story->priList, $child->pri, $child->pri);?>'><?php echo zget($lang->story->priList, $child->pri, $child->pri);?></span></td>
            <td class='c-product'><?php echo zget($programs, $child->program, '');?></td>
            <td class='c-product'><?php echo $child->productTitle;?></td>
            <td class='c-name nobr'>
                <?php echo '<span class="label label-badge label-light" title="' . $this->lang->story->children .'">' . $this->lang->story->childrenAB . '</span> ' . html::a($storyLink, $child->title, null, "style='color: $child->color'");?>
            </td>
            <td class='c-plan'><?php echo $child->planTitle;?></td>
            <td class='c-user'><?php echo zget($users, $child->openedBy);?></td>
            <td class='c-hours'><?php echo $child->estimate;?></td>
            <td class='c-status'><span class='status-story status-<?php echo $child->status;?>'> <?php echo $this->processStatus('story', $child);?></span></td>
            <td class='c-stage'><?php echo zget($lang->story->stageList, $child->stage);?></td>
            <td class='c-actions'>
              <?php
              $vars = "story={$child->id}";
              common::printIcon('story', 'change',     $vars, $child, 'list', 'fork', '', '', '', '', '', $child->program);
              common::printIcon('story', 'review',     $vars, $child, 'list', 'glasses', '', '', '', '', '', $child->program);
              common::printIcon('story', 'close',      $vars, $child, 'list', '', '', 'iframe', true, '', '', $child->program);
              common::printIcon('story', 'edit',       $vars, $child, 'list', '', '', '', '', '', '', $child->program);
              common::printIcon('story', 'createCase', "productID=$child->product&branch=$child->branch&module=0&from=&param=0&$vars", $child, 'list', 'sitemap', '', '', '', '', '', $child->program);
              ?>
            </td>
          </tr>
          <?php $i ++;?>
          <?php endforeach;?>
        <?php endif;?>
        <?php endforeach;?>
      </tbody>
    </table>
    <div class="table-footer">
      <?php if($canBatchAction):?>
      <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
      <?php endif;?>
      <div class="table-actions btn-toolbar">
        <?php
        if($canBatchEdit)
        {
            $actionLink = $this->createLink('story', 'batchEdit');
            $misc       = "data-form-action='$actionLink'";
            echo html::commonButton($lang->edit, $misc);
        }
        ?>
        <?php if($canBatchReview):?>
        <div class="btn-group dropup">
          <button type='button' class='btn' data-toggle='dropdown'><?php echo $lang->story->review;?> <span class='caret'></span></button>
          <ul class='dropdown-menu'>
            <?php
            unset($lang->story->reviewResultList['']);
            unset($lang->story->reviewResultList['revert']);
            foreach($lang->story->reviewResultList as $key => $result)
            {
                $actionLink = $this->createLink('story', 'batchReview', "result=$key", '', '', $story->program);
                if($key == 'reject')
                {
                    echo "<li class='dropdown-submenu'>";
                    echo html::a('#', $result, '', "id='rejectItem'");
                    echo "<ul class='dropdown-menu'>";
                    unset($lang->story->reasonList['']);
                    unset($lang->story->reasonList['subdivided']);
                    unset($lang->story->reasonList['duplicate']);

                    foreach($lang->story->reasonList as $key => $reason)
                    {
                        $actionLink = $this->createLink('story', 'batchReview', "result=reject&reason=$key", '', '', $story->program);
                        echo "<li>";
                        echo html::a('#', $reason, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"");
                        echo "</li>";
                    }
                    echo '</ul></li>';
                }
                else
                {
                  echo '<li>' . html::a('#', $result, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . '</li>';
                }
            }
            ?>
          </ul>
        </div>
        <?php endif;?>
        <?php if($canBatchAssignTo):?>
        <div class="btn-group dropup">
          <button data-toggle="dropdown" type="button" class="btn"><?php echo $lang->story->assignedTo?> <span class="caret"></span></button>
          <?php
          $withSearch = count($users) > 10;
          $actionLink = $this->createLink('story', 'batchAssignTo', '', '', '', $story->program);
          echo html::select('assignedTo', $users, '', 'class="hidden"');
          if($withSearch)
          {
              echo "<div class='dropdown-menu search-list search-box-sink' data-ride='searchList'>";
              echo '<div class="input-control search-box has-icon-left has-icon-right search-example">';
              echo '<input id="userSearchBox" type="search" class="form-control search-input" autocomplete="off" />';
              echo '<label for="userSearchBox" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>';
              echo '<a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>';
              echo '</div>';
              $usersPinYin = common::convert2Pinyin($users);
          }
          else
          {
              echo "<div class='dropdown-menu search-list'>";
          }
          echo '<div class="list-group">';
          foreach($users as $key => $value)
          {
              if(empty($key) or $key == 'closed') continue;
              $searchKey = $withSearch ? ('data-key="' . zget($usersPinYin, $value, '') . " @$key\"") : "data-key='@$key'";
              echo html::a('javascript:$(".table-actions #assignedTo").val("' . $key . '");setFormAction("' . $actionLink . '")', '<i class="icon icon-person icon-sm"></i> ' . $value, '', $searchKey);
          }
          echo "</div>";
          echo "</div>";
          ?>
        </div>
        <?php endif;?>
        <?php if($canBatchClose):?>
        <?php
        $actionLink = $this->createLink('story', 'batchClose');
        $misc = "data-form-action=\"$actionLink\"";
        echo html::commonButton($lang->close, $misc);
        ?>
        <?php endif;?>
      </div>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
