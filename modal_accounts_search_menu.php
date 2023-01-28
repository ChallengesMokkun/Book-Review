<?php
  if(basename($_SERVER['PHP_SELF']) === 'modal_accounts_search_menu.php'){
    header('Location:index.php');
    exit();
  }
?>
<h2 class="p-modal__sub-heading c-sub-heading">検索オプション</h2>
<i class="fa-solid fa-square-xmark p-modal__fonticon c-font-icon js-modal-quit"></i>
<form action="" method="get" class="p-search-menu js-search-menu">
  <button class="p-search-menu__btn c-btn c-btn--inactive c-btn--l js-reset-btn" type="button">条件をリセット</button>
  <div class="p-search-menu__search-row">
    <label for="word" class="p-search-menu__form-label c-form-label c-form-label--search">ID検索(スペース区切りで複数)</label>
    <input type="text" name="word" class="p-search-menu__textform c-textform c-textform--search js-textform" placeholder="ID" id="word" value="<?php echo keepTextData($word,'word',true); ?>">
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="words_logic_flag" value="0" id="words_and" class="p-search-menu__radioform c-radioform js-radioform" <?php keepSelectData($words_logic_flag,'words_logic_flag',0); ?>>
        <label for="words_and" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">すべて一致</label>
      </span>
      <span>
        <input type="radio" name="words_logic_flag" value="1" id="words_or" class="p-search-menu__radioform c-radioform js-radioform" <?php keepSelectData($words_logic_flag,'words_logic_flag',1); ?>>
        <label for="words_or" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">いずれか一致</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <label for="reported_num" class="p-search-menu__form-label c-form-label c-form-label--search">通報された回数(以上)</label>
    <input type="text" name="reported_num" class="p-search-menu__textform c-textform c-textform--search js-textform" placeholder="回数" id="reported_num" value="<?php echo keepTextData($reported_num,'reported_num',true); ?>">
  </div>
  <div class="p-search-menu__search-row">
    <label for="delete_num" class="p-search-menu__form-label c-form-label c-form-label--search">コメント削除された回数(以上)</label>
    <input type="text" name="delete_num" class="p-search-menu__textform c-textform c-textform--search js-textform" placeholder="回数" id="delete_num" value="<?php echo keepTextData($delete_num,'delete_num',true); ?>">
  </div>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__checkbox-wrapper">
      <span>
        <input type="checkbox" name="delete_appear" id="delete_appear" value="1" class="p-search-menu__checkboxform c-checkboxform c-checkboxform--search js-checkboxform" <?php keepSelectData($delete_appear,'delete_appear',1); ?>>
        <label for="delete_appear" class="p-search-menu__checkbox-label c-checkboxform__label c-checkboxform__label--search">削除されたIDを表示</label>
      </span>
    </div>
  </div>
  <div class="p-search-menu__search-row">
    <div class="p-search-menu__radio-wrapper">
      <span>
        <input type="radio" name="logic_flag" value="0" id="and" class="p-search-menu__radioform c-radioform js-radioform" <?php keepSelectData($logic_flag,'logic_flag',0); ?>>
        <label for="and" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">条件すべて一致</label>
      </span>
      <span>
        <input type="radio" name="logic_flag" value="1" id="or" class="p-search-menu__radioform c-radioform js-radioform" <?php keepSelectData($logic_flag,'logic_flag',1); ?>>
        <label for="or" class="p-search-menu__radio-label c-radioform__label c-radioform__label--search">条件いずれか一致</label>
      </span>
    </div>
  </div>
  <input type="submit" value="検索する" class="p-search-menu__btn c-btn c-btn--active c-btn--l js-modal-quit">
</form>
