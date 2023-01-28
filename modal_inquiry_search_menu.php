<?php
  if(basename($_SERVER['PHP_SELF']) === 'modal_inquiry_search_menu.php'){
    header('Location:index.php');
    exit();
  }
?>
<h2 class="p-modal__sub-heading c-sub-heading">検索オプション</h2>
<i class="fa-solid fa-square-xmark p-modal__fonticon c-font-icon js-modal-quit"></i>
<form action="" method="get" class="p-search-menu js-search-menu">
  <button class="p-search-menu__btn c-btn c-btn--inactive c-btn--l js-reset-btn" type="button">条件をリセット</button>
  <div class="p-search-menu__search-row">
    <label for="word" class="p-search-menu__form-label c-form-label c-form-label--search">語句(スペース区切りで複数)</label>
    <input type="text" name="word" placeholder="問い合わせ詳細" class="p-search-menu__textform c-textform c-textform--search js-textform" id="word" value="<?php echo keepTextData($word,'word',true); ?>">
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="words_logic_flag" value="0" class="p-search-menu__radioform c-radioform js-radioform" id="words_and" <?php keepSelectData($words_logic_flag,'words_logic_flag',0); ?>>
        <label for="words_and" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">すべて一致</label>
      </span>
      <span>
        <input type="radio" name="words_logic_flag" value="1" class="p-search-menu__radioform c-radioform js-radioform" id="words_or" <?php keepSelectData($words_logic_flag,'words_logic_flag',1); ?>>
        <label for="words_or" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">いずれか一致</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <label for="subject_id" class="p-search-menu__form-label c-form-label c-form-label--search">内容</label>
    <select name="subject_id" id="subject_id" class="p-search-menu__selectform c-selectform c-selectform--search js-selectform">
      <option value="0" <?php keepSelectData($subject_id,'subject_id',0,true); ?>>すべて</option>
      <?php
        if(!empty($subject_list)){
          foreach($subject_list as $key => $val){
      ?>
      <option value="<?php echo $val['subject_id']; ?>" <?php keepSelectData($subject_id,'subject_id',$val['subject_id'],true,false); ?>><?php echo $val['subject']; ?></option>
      <?php
          }
        }
      ?>
    </select>
  </div>
  <div class="p-search-menu__search-row">
    <p>期間</p>
    <input type="datetime-local" name="start" id="start" class="p-search-menu__datetimelocalform c-datetimelocalform c-datetimelocalform--search js-datetimelocalform" value="<?php echo datetime2Local(keepTextData($start,'start',true)); ?>">
    <label for="start" class="p-search-menu__form-label c-form-label c-form-label--search">から</label>
    <input type="datetime-local" name="finish" id="finish" class="p-search-menu__datetimelocalform c-datetimelocalform c-datetimelocalform--search js-datetimelocalform" value="<?php echo datetime2Local(keepTextData($finish,'finish',true)); ?>">
    <label for="finish" class="p-search-menu__form-label c-form-label c-form-label--search">まで</label>
  </div>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__checkbox-wrapper">
      <span>
        <input type="checkbox" name="delete_appear" class="p-search-menu__checkboxform c-checkboxform c-checkboxform--search js-checkboxform" id="delete_appear" value="1" <?php keepSelectData($delete_appear,'delete_appear',1); ?>>
        <label for="delete_appear" class="p-search-menu__checkbox-label c-checkboxform__label c-checkboxform__label--search">対応済を表示</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="logic_flag" value="0" class="p-search-menu__radioform c-radioform js-radioform" id="and" <?php keepSelectData($logic_flag,'logic_flag',0); ?>>
        <label for="and" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">条件すべて一致</label>
      </span>
      <span>
        <input type="radio" name="logic_flag" value="1" class="p-search-menu__radioform c-radioform js-radioform" id="or" <?php keepSelectData($logic_flag,'logic_flag',1); ?>>
        <label for="or" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">条件いずれか一致</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="flow_flag" value="0" class="p-search-menu__radioform c-radioform js-radioform" id="desc" <?php keepSelectData($flow_flag,'flow_flag',0); ?>>
        <label for="desc" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">新しい順</label>
      </span>
      <span>
        <input type="radio" name="flow_flag" value="1" class="p-search-menu__radioform c-radioform js-radioform" id="asc" <?php keepSelectData($flow_flag,'flow_flag',1); ?>>
        <label for="asc" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">古い順</label>
      </span>
    </div>
  </div>
  <input type="submit" value="検索する" class="p-search-menu__btn c-btn c-btn--active c-btn--l js-modal-quit">
</form>