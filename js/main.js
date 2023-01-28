$(function(){
  //フッター下部固定
  var $footer = $('.l-footer');
  if(window.innerHeight > $footer.outerHeight() + $footer.offset().top){
    $footer.attr('style','position: fixed; top: ' + (window.innerHeight - $footer.outerHeight()) + 'px;');
  }

  //文字数カウント
  var $textarea = $('.js-textarea');
  
  $textarea.on('keyup',function(){
    var $count = $(this).siblings('.js-count'),
        $num = $count.find('.js-count-num'),
        limit = $count.find('.js-count-limit').text();

    $num.text($(this).val().length);

    if($(this).val().length > limit){
      $count.addClass('err');
      $(this).addClass('err');
    }else{
      $count.removeClass('err');
      $(this).removeClass('err');
    }
  });

  //画像ライブビュー
  var $img_form = $('.js-edit-img-form');

  $img_form.on('change',function(){
    var $img = $(this).siblings('.js-edit-img'),
        fileReader = new FileReader,
        file = this.files[0];
    
    fileReader.onload = function(event){
      $img.attr('src',event.target.result).show();
    }

    fileReader.readAsDataURL(file);
  });

  //全てのボックスにチェックする+チェックボックスの個数カウント
  $('#js-check-all').on('click',function() {
    $('.js-record-checkbox').prop('checked', $(this).is(':checked') );

  });

  $('.js-record-checkbox').on('click',function() {
    var boxes_num = $('.js-record-checkbox').length; //全チェックボックスの数を取得
    var checked_num  = $('.js-record-checkbox:checked').length; //チェックされているチェックボックスの数を取得
    if( checked_num === boxes_num ) {
      $('#js-check-all').prop('checked',true);
    } else {
      $('#js-check-all').prop('checked',false);
    }
  });

  //チェックボックスの個数カウント
  $('.js-search-checkbox').on('change',function(){
    var checked_num = $('.js-record-checkbox:checked').length,
        check_limit = $('.js_check_limit').text() || null;
    if(checked_num > 0){
      $('.js-check-num').text(checked_num + '件選択中').show();

      if(check_limit !== null){
        if(checked_num > check_limit){
          $('.js-check-num').addClass('err');
        }else{
          $('.js-check-num').removeClass('err');
        }
      }

    }else{
      $('.js-check-num').hide();
    }
  });

  //クリックしたら隠し要素を表示させる(管理人画面)
  var $open_btn = $('.js-open-btn');
  $open_btn.on('click',function(){
    var $opened_area = $(this).siblings('.js-opened-area');
    $opened_area.toggle();
  });

  //評価
  var $score_input = $('.js-score-input');
  $score_input.on('input',function(){
    var $indicate = $(this).siblings('.js-score-indicate');
    $indicate.text($(this).val());
  });

  //お気に入り登録・削除
  var $fav_heart = $('.js-favorite') || null,
      fav_content = $fav_heart.data('fav_content') || null,
      $fav_num = $fav_heart.closest('.p-content-header__icon-wrapper').siblings('.js-fav-num') || null;
  
  if(fav_content !== null && fav_content !== undefined){
    $fav_heart.on('click',function(){
      $.ajax({
        type: 'POST',
        url: 'ajax_fav.php',
        data: {b_id : fav_content}
      }).done(function(data){
        $fav_heart.toggleClass('c-fonticon--active-fav c-fonticon--inactive-fav');
        if($fav_heart.hasClass('c-fonticon--active-fav')){
          $fav_num.text(Number(data));
        }else{
          $fav_num.text(Number(data));
        }
      });
    });
  }

  //ヘッダーメニュー
  $('.js-header-push').on('click',function(){
    $(this).toggleClass('js-active');
    $('.js-header-menu-nav').toggleClass('js-active');
  });

  //メイン画像切り替え(コンテンツページ)
  var $sub_img = $('.js-sub-img');
  $sub_img.on('click',function(){
    $('.js-main-img').attr('src',$(this).attr('src'));
  });

  //モーダル
  $('.js-modal-trigger').on('click',function(){
    var modal_width = $('.js-modal-window').innerWidth(),
        modal_height = $('.js-modal-window').innerHeight(),
        window_width = $(window).width(),
        window_height = $(window).height();

    if(window_height > modal_height){
      $('.js-modal-window').attr('style',
      'left: ' + (window_width / 2 - modal_width / 2) + 'px; top: ' + (window_height / 2 - modal_height / 2) + 'px'
      );
    }else{
      $('.js-modal-window').attr('style',
      'left: ' + (window_width / 2 - modal_width / 2) + 'px; top: 15px');
    }

    $('.js-modal-back').attr('style',
      'height: ' + $(document).height() + 'px'
    );

    $('.js-modal-back').show();
    $('.js-modal-window').show();
  });
  $('.js-modal-quit').on('click',function(){
    $('.js-modal-back').hide();
    $('.js-modal-window').hide();
  });

  //完了メッセージ
  var $success_msg = $('.js-success-msg');
  if($success_msg.text().replace(/[\s　]+/g,'').length){
    var window_width = $(window).width(),
        window_height = $(window).height(),
        msg_width = $success_msg.innerWidth(),
        msg_height = $success_msg.innerHeight();

    $success_msg.attr('style',
    'left: ' +  (window_width / 2 - msg_width / 2) + 'px; top: ' + (window_height / 2 - msg_height / 2) + 'px'
    );
    $success_msg.addClass('active');
    setTimeout(function(){
      $success_msg.removeClass('active');
    },2000);
  }

  //検索条件リセット
  $('.js-reset-btn').on('click',function(){
    $('.js-search-menu').find('.js-textform','.js-datetimelocalform').val('');
    $('.js-search-menu').find('.js-radioform').val(['0']);
    $('.js-search-menu').find('.js-selectform').val('0');
    $('.js-search-menu').find('.js-checkboxform').prop('checked',false);
  });
  
  //パスワード確認
  $('.js-pass-show').on('click',function(){
    $(this).toggleClass('fa-eye fa-eye-slash');
    var $passform = $(this).siblings('.js-passform');
    if($passform.attr('type') === 'password'){
      $passform.attr('type','text');
    }else{
      $passform.attr('type','password');
    }
  });

  //いいね機能
  var $good_cmd = $('.js-good') || null;

  $good_cmd.on('click',function(){
    $good_icon = $(this).find('.js-good-icon') || null,
    good_comment = $(this).data('good_comment') || null,
    already_good = $good_icon.data('already_good') || null,
    $good_num = $(this).siblings('.js-good-num') || null;

    if(good_comment !== null && good_comment !== undefined){
      $.ajax({
        type: 'POST',
        url: 'ajax_good.php',
        data: {
          com_id: good_comment,
          good_flag: already_good
        }
      }).done(function(data){
        $good_icon.toggleClass('c-fonticon--active-good');
        if($good_icon.hasClass('c-fonticon--active-good')){
          $good_num.text(Number(data));
          $good_icon.data('already_good',true);
        }else{
          $good_num.text(Number(data));
          $good_icon.data('already_good',false);
        }
      });
    }
  });
});


