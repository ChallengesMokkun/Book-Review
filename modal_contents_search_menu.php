<?php
  if(basename($_SERVER['PHP_SELF']) === 'modal_contents_search_menu.php'){
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
    <input type="text" name="word" placeholder="検索ワード" class="p-search-menu__textform c-textform c-textform--search js-textform" id="word" value="<?php echo keepTextData($word,'word',true); ?>">
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
    <label for="g_id" class="p-search-menu__form-label c-form-label c-form-label--search">ジャンル</label>
    <select name="g_id" id="g_id" class="p-search-menu__selectform c-selectform c-selectform--search js-selectform">
      <option value="0" <?php keepSelectData($g_id,'g_id',0,true); ?>>すべて</option>
      <?php
      if(!empty($genres)){
        foreach($genres as $key => $val){
      ?>
      <option value="<?php echo $val['g_id']; ?>" <?php keepSelectData($g_id,'g_id',$val['g_id'],true); ?>><?php echo $val['genre']; ?></option>
      <?php
          }
        }
      ?>
    </select>
  </div>
  <div class="p-search-menu__search-row">
    <label for="category" class="p-search-menu__form-label c-form-label c-form-label--search">カテゴリー</label>
    <div class="p-search-menu__checkboxes" id="category">
      <?php
        if(!empty($categories)){
          foreach($categories as $key => $val){
      ?>
      <div class="p-search-menu__box-wrapper">
        <input type="checkbox" name="category[]" class="p-search-menu__checkboxform c-checkboxform c-checkboxform--search js-checkboxform" id="c_id<?php echo $val['c_id']; ?>" value="<?php echo $val['c_id']; ?>" <?php keepSelectComplex($category,'category',$val['c_id']); ?>>
        <label for="c_id<?php echo $val['c_id']; ?>" class="p-search-menu__checkbox-label c-checkboxform__label c-checkboxform__label--search"><?php echo $val['c_name']; ?></label>
      </div>
      <?php
          }
        }
      ?>
    </div>
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="category_flag" value="0" class="p-search-menu__radioform c-radioform js-radioform" id="category_and" <?php keepSelectData($category_flag,'category_flag',0); ?>>
        <label for="category_and" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">すべて一致</label>
      </span>
      <span>
        <input type="radio" name="category_flag" value="1" class="p-search-menu__radioform c-radioform js-radioform" id="category_or" <?php keepSelectData($category_flag,'category_flag',1); ?>>
        <label for="category_or" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">いずれか一致</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <label for="s_id" class="p-search-menu__form-label c-form-label c-form-label--search">出版社</label>
    <select name="s_id" id="s_id" class="p-search-menu__selectform c-selectform c-selectform--search js-selectform">
      <option value="0" <?php keepSelectData($s_id,'s_id',0,true); ?>>すべて</option>
      <?php
      if(!empty($sellers)){
        foreach($sellers as $key => $val){
      ?>
      <option value="<?php echo $val['s_id']; ?>" <?php keepSelectData($s_id,'s_id',$val['s_id'],true); ?>><?php echo $val['s_name']; ?></option>
      <?php
          }
        }
      ?>
    </select>
  </div>
  <div class="p-search-menu__search-row">
    <label for="year" class="p-search-menu__form-label c-form-label c-form-label--search">発売年</label>
    <input type="text" name="year" id="year" class="p-search-menu__textform c-textform c-textform--search js-textform" placeholder="西暦(半角数字)" value="<?php echo keepTextData($year,'year',true); ?>">
  </div>
  <?php if(basename($_SERVER['PHP_SELF']) === 'admin_content_search_secret.php'){ ?>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__checkbox-wrapper">
      <span>
        <input type="checkbox" name="delete_appear" class="p-search-menu__checkboxform c-checkboxform c-checkboxform--search js-checkboxform" id="delete_appear" value="1" <?php keepSelectData($delete_appear,'delete_appear',1); ?>>
        <label for="delete_appear" class="p-search-menu__checkboxform-label c-checkboxform__label c-checkboxform__label--search">削除コンテンツを表示</label>
      </span>
    </div>
  </div>
  <?php } ?>
  <div class="p-search-menu__search-row">
    <label for="flow_flag" class="p-search-menu__form-label c-form-label c-form-label--search">並び順</label>
    <select name="flow_flag" id="flow_flag" class="p-search-menu__selectform c-selectform c-selectform--search js-selectform">
      <option value="0" <?php keepSelectData($flow_flag,'flow_flag',0,true); ?>>登録が新しい順</option>
      <option value="1" <?php keepSelectData($flow_flag,'flow_flag',1,true); ?>>評価が高い順</option>
      <option value="2" <?php keepSelectData($flow_flag,'flow_flag',2,true); ?>>お気に入りが多い順</option>
      <option value="3" <?php keepSelectData($flow_flag,'flow_flag',3,true); ?>>コメント数が多い順</option>
      <option value="4" <?php keepSelectData($flow_flag,'flow_flag',4,true); ?>>閲覧が多い順</option>
      <option value="5" <?php keepSelectData($flow_flag,'flow_flag',5,true); ?>>発売が新しい順</option>
      <option value="6" <?php keepSelectData($flow_flag,'flow_flag',6,true); ?>>発売が古い順</option>
      <option value="7" <?php keepSelectData($flow_flag,'flow_flag',7,true); ?>>AtoZ あいうえお順</option>
      <option value="8" <?php keepSelectData($flow_flag,'flow_flag',8,true); ?>>ZtoA あいうえお逆順</option>
    </select>
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
  <input type="submit" value="検索する" class="p-search-menu__btn c-btn c-btn--active c-btn--l js-modal-quit">
</form>