function detailFormatter(index, row) {
  let num_ses = row._data.numses;
  let convocation = row._data.convocation;
  let nonce = row._data.nonce;
  let file_id = row._data.docid;

  jQuery.ajax({
    url: ajaxurl,
    type: "POST",
    dataType: "html",
    data: {
      action: "khor_golos_admin_subtable",
      nonce,
      doc_id : file_id,
      data_convocation: convocation,
      data_session: num_ses,
    },
    success: function (data) {
      jQuery('.admin_subrow_session[data-index="' + index + '"]')
        .next(".detail-view")
        .children("td")
        .append(data);
    },
  });

  addHeaderTitleInSubtableOpen(row._id);
  addMetaInfoInSubtableOpen(row._id, num_ses, convocation);

  setTimeout(function () {
    showMetaInfoForClick(num_ses, convocation);
    editClickChangeGolos();
    saveChangeGolos();
    closeEditGolos();
    viewFileGolos();
    openMediaUploadFileGolos();
    clearInputClickUploadFile()
  }, 800);
}

function addMetaInfoInSubtableOpen(id, num_ses, convocation) {
  let nonce = '';
  jQuery.ajax({
    url: ajaxurl,
    type: "POST",
    dataType: "html",
    data: {
      action: "khor_golos_admin_subtable_get_meta_info",
      data_session: num_ses,
      data_convocation: convocation
    },
    success: function (data) {
      jQuery('#' + id).next('.detail-view').children('td').prepend(data);
    },
  });
}

function addHeaderTitleInSubtableOpen(id) {
  jQuery('#' + id).next('.detail-view').children('td').prepend('<tr class="admin_subtable"><th>№ порядку</th><th colspan="8">№ голосування</th><th>Повна назва</th><th>Скор.назва</th><th>Посилання</th><th>Змінити</th></tr>');
}

function editClickChangeGolos() {
  jQuery(".admin_subtable-trigger").on("click", function () {
    jQuery(this).hide();
    jQuery(this).next(".admin_subtable-see-button").hide();
    jQuery(this)
      .closest("td")
      .children(".stack-button-active")
      .css("display", "flex");

    let fname = jQuery(this).closest("tr").find(".pdname_content");
    let sname = jQuery(this).closest("tr").find(".glname_content");
    let golos_link = jQuery(this).closest("tr").find(".admin_subtable_link");
    let closeButtonGolosUpload = jQuery(this).closest("tr").find(".remove_file_golos_button");
    let inputButtonGolosUpload = jQuery(this).closest("tr").find(".admin_subtable_link_media_wrapper");

    fname.attr("contenteditable", "true");
    sname.attr("contenteditable", "true");
    golos_link.prop("disabled", false).css('height', '100px');
    closeButtonGolosUpload.css('display', 'block');
    inputButtonGolosUpload.css('display', 'flex');

    fname.addClass("active-change");
    sname.addClass("active-change");
  });
}

function saveChangeGolos() {
  jQuery(".admin_subtable-save-button").click(function () {
    let fname = jQuery(this).closest("tr").find(".pdname_content");
    let sname = jQuery(this).closest("tr").find(".glname_content");
    let golos_link = jQuery(this).closest("tr").find(".admin_subtable_link");
    let closeButtonGolosUpload = jQuery(this).closest("tr").find(".remove_file_golos_button");
    let inputButtonGolosUpload = jQuery(this).closest("tr").find(".admin_subtable_link_media_wrapper");

    fname.attr("contenteditable", "false");
    sname.attr("contenteditable", "false");
    golos_link.prop("disabled", true).css('height', 'auto');
    closeButtonGolosUpload.hide();
    inputButtonGolosUpload.hide();
    fname.removeClass("active-change");
    sname.removeClass("active-change");

    jQuery(this).parent(".stack-button-active").css("display", "none");
    jQuery(this)
      .closest(".admin_subtable-active-button")
      .children("a")
      .css("display", "block");

    var fname_content = jQuery(this)
      .closest("tr")
      .find(".pdname_content")
      .text();
    var sname_content = jQuery(this)
      .closest("tr")
      .find(".glname_content")
      .text();
    var golos_link_content = jQuery(this)
      .closest("tr")
      .find(".admin_subtable_link")
      .attr("value");
    var row_id = jQuery(this).closest("tr").attr("data-id");

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      //dataType: 'html',
      data: {
        action: "khor_golos_admin_subtable_save_row",
        pdname: fname_content,
        glname: sname_content,
        gllink: golos_link_content,
        row_id: row_id,
      },
      success: function (data) {
        alert(data);
      },
    });
  });
}

function closeEditGolos() {
  jQuery(".admin_subtable-cancel-button").on("click", function () {
    let fname = jQuery(this).closest("tr").find(".pdname_content");
    let sname = jQuery(this).closest("tr").find(".glname_content");
    let golos_link = jQuery(this).closest("tr").find(".admin_subtable_link");
    let closeButtonGolosUpload = jQuery(this).closest("tr").find(".remove_file_golos_button");
    let inputButtonGolosUpload = jQuery(this).closest("tr").find(".admin_subtable_link_media_wrapper");

    fname.attr("contenteditable", "false");
    sname.attr("contenteditable", "false");
    golos_link.prop("disabled", true).css('height', 'auto');
    closeButtonGolosUpload.hide();
    inputButtonGolosUpload.hide();
    fname.removeClass("active-change");
    sname.removeClass("active-change");

    jQuery(this).parent(".stack-button-active").css("display", "none");
    jQuery(this)
      .closest(".admin_subtable-active-button")
      .children("a")
      .css("display", "block");
  });
}

function viewFileGolos() {
  jQuery(".admin_subtable-see-button").on("click", function () {
    let link = jQuery(this).closest("tr").find(".admin_subtable_link").val();
    
    if (link === '') {
      alert('Немає даних для відображення');
    } else {
      let regUrl = /:\/\/(.[^/]+)/;
      let regId = /d\/([^/]+)/;
      let urlFile = link.match(regUrl)[1];

      if (urlFile == 'drive.google.com') {
        let idFile = link.match(regId)[1];
        jQuery("#wpcontent").append(
          "<div class='view-golos-file-modal'><div class='modal-file-view-wrapper'><span class='close-modal-file-view'><i class='fas fa-times'></i></span><iframe src='https://drive.google.com/file/d/" + idFile + "/preview'></iframe></div></div>");
      } else {
        jQuery("#wpcontent").append(
          "<div class='view-golos-file-modal'><div class='modal-file-view-wrapper'><span class='close-modal-file-view'><i class='fas fa-times'></i></span><iframe src='https://docs.google.com/viewer?url=" + link + "&embedded=true' style='width:100%; height:100%;' frameborder='0'></iframe></div></div>");
      }
    }
    clickCloseButton(".view-golos-file-modal");
  });
}

function clickCloseButton(parentClass) {
  jQuery(".close-modal-file-view").on("click", function () {
    jQuery(this)
      .parents(parentClass)
      .remove();
  });
}

function showMetaInfoForClick(num_session, convocation) {

  jQuery('.admin_subtable-info-wrpapper').on('click', function (e) {
    let dataSend = {
      data_session: num_session,
      data_convocation: convocation,
      data_pdname: jQuery(this).closest("tr").attr('data-pd'),
      data_glnum: jQuery(this).closest("tr").attr('data-gl'),
    };
    let titleNameGolosAdminModalWindow = jQuery(this).closest("tr").prev("tr").find(".pdname_content").text();
    let clickTarget = e.target.closest('div');
    let ajaxQueryConstructor = function (actionType) {
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          dataSend: dataSend,
          //nonce: 'nonce',
          action: actionType,
          sendType: 'khor_golos_admin_subtable_ajax_click_action',
        },
        success: function (data) {
          let decodeJson = JSON.parse(data);
          jQuery("#wpcontent").append(
            `<div class='view-golos-subtable-modal'>` +
            `<div class = 'view-golos-subtable-modal__show'>` +
            `<span class = 'close-modal-file-view'>` +
            `<i class = 'fas fa-times'></i>` +
            `</span>` +
            `<div class='view-public-golos-meta-table__title'>${titleNameGolosAdminModalWindow}</div>` +
            `<div class='view-public-golos-meta-table__title-info'>` +
            `<span>Сесія №: ${dataSend.data_session}</span>` +
            `</div>` +
            `</div>` +
            `</div>`
          );
          for (let i = 0; i < decodeJson.length; i++) {
            jQuery(".view-golos-subtable-modal__show").append(
              `<div class='row view-golos-subtable-modal__row'>` +
              `<div class='col-8 view-golos-subtable-modal__deput'>` + decodeJson[i].ok_dp_name + `</div>` +
              `<div class='col-4 view-golos-subtable-modal__result'>` + decodeJson[i].ok_dp_golos + `</div>` +
              `</div>` +
              `</div>`
            );
          }
          clickCloseButton(".view-golos-subtable-modal");
        },
      });
    }
    if (this.contains(clickTarget)) {
      let attrClickTarget = jQuery(e.target).closest('div').attr('data-class');
      let attrClickTargetCheckEmpty = jQuery(e.target).closest('div').attr('data-empty');
      let actionType;

      switch (attrClickTarget) {
        case 'admin_subtable-yes-golos':
          actionType = 'admin_subtable_ajax_yes_golos';
          break;
        case 'admin_subtable-no-golos':
          actionType = 'admin_subtable_ajax_no_golos';
          break;
        case 'admin_subtable-utr-golos':
          actionType = 'admin_subtable_ajax_utr_golos';
          break;
        case 'admin_subtable-ng-golos':
          actionType = 'admin_subtable_ajax_ng_golos';
          break;
        case 'admin_subtable-all-golos':
          actionType = 'admin_subtable_ajax_all_golos';
          break;
        default:
          actionType = false;
          break;
      }
      if (attrClickTargetCheckEmpty != 1 && actionType != false) {
        ajaxQueryConstructor(actionType);
      }
    } else {
      alert('Помилка відображення інформації голосування');
    }
  });

}

function openMediaUploadFileGolos() {
  jQuery('.admin_subtable_link_media').on('click', function (e) {
    e.preventDefault();
    let file_frame;
    if (file_frame) {
      file_frame.open();
    }
    file_frame = wp.media({
      title: 'Оберіть файл',
      multiple: false,
      library: {
        type: 'application',
      }
    });

    // When an image is selected in the media frame...
    file_frame.on('select', function () {
      // Get media attachment details from the frame state
      let attachment = file_frame.state().get('selection').first().toJSON();
      let activeElement = $(this).context.activeElement;
      console.log(attachment.url);
      $(activeElement).parents('.golos-file-upload-form').find('.admin_subtable_link').val(attachment.url);
    });

    file_frame.open();
  });
}

function clearInputClickUploadFile() {
  jQuery('.remove_file_golos_button').on('click', function (e) {
    jQuery(this).prev('.admin_subtable_link').val('');
  });
}


(function ($) {
  "use strict";

  // Change/Delete function
  window.operateEvents = {
    "click .change": function (e, value, row, index) {
      let currentVideoLink = $(e.currentTarget)
      .closest("tr")
      .find(".ok-admin-video-url-link a").attr("href");
      let currentSolutionLink = $(e.currentTarget)
      .closest("tr")
      .find(".ok-admin-solution-link a").attr("href");
      let videoLink = $(e.currentTarget)
        .closest("tr")
        .find(".ok-admin-video-url-link").attr("contenteditable", "true").addClass("active-change");
      let solutionLink = $(e.currentTarget)
        .closest("tr")
        .find(".ok-admin-solution-link").attr("contenteditable", "true").addClass("active-change");
      
      $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link a").text(currentVideoLink);
      $(e.currentTarget).closest("tr").find(".ok-admin-solution-link a").text(currentSolutionLink);

      $(e.currentTarget).closest('.ok-control-table-files').hide();
      $(e.currentTarget).closest('.ok-control-table-files').siblings('.ok-control-table-files-second-group').css('display', 'flex');
    },
    "click .ok-control-table-files-save-button": function (e, value, row, index) {
      let currentVideoLink = $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link a").text();
      let currentSolutionLink = $(e.currentTarget).closest("tr").find(".ok-admin-solution-link a").text();
      
      $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link a").attr("href", currentVideoLink);
      $(e.currentTarget).closest("tr").find(".ok-admin-solution-link a").attr("href", currentSolutionLink);

      let videoLink = $(e.currentTarget)
        .closest("tr")
        .find(".ok-admin-video-url-link a").attr('href');
      let solutionLink = $(e.currentTarget)
        .closest("tr")
        .find(".ok-admin-solution-link a").attr('href');

      $.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: 'JSON',
        data: {
          data_video_url: videoLink,
          data_solutions_url: solutionLink,
          data_id: row._data.docid,
          action: "khor_golos_change_video_solutions",
        },
        success: function (data) {
            alert(data.message);
            $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link a").text('Переглянути');
            $(e.currentTarget).closest("tr").find(".ok-admin-solution-link a").text('Переглянути');
            $(e.currentTarget).closest('.ok-control-table-files-second-group').hide();
            $(e.currentTarget).closest('.ok-control-table-files-second-group').siblings('.ok-control-table-files').css('display', 'flex');
            $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link").attr("contenteditable", "false").removeClass("active-change");
            $(e.currentTarget).closest("tr").find(".ok-admin-solution-link").attr("contenteditable", "false").removeClass("active-change");
        },
      });
    },
    "click .ok-control-table-files-cancel-button": function (e, value, row, index) {
      $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link a").text('Переглянути');
      $(e.currentTarget).closest("tr").find(".ok-admin-solution-link a").text('Переглянути');
      $(e.currentTarget).closest('.ok-control-table-files-second-group').hide();
      $(e.currentTarget).closest('.ok-control-table-files-second-group').siblings('.ok-control-table-files').css('display', 'flex');
      $(e.currentTarget).closest("tr").find(".ok-admin-video-url-link").attr("contenteditable", "false").removeClass("active-change");
      $(e.currentTarget).closest("tr").find(".ok-admin-solution-link").attr("contenteditable", "false").removeClass("active-change");
    },
    "click .remove": function (e, value, row, index) {
      let file_id = row._data.docid;
      let filename = row._data.filename;
      let num_ses = row._data.numses;
      let convocation = row._data.convocation;
      let nonce = row._data.nonce;

      $("#wpcontent").append(
        "<div class='confirm-delete-session'><div class='delete-session__wrapper'>Видалити всі дані поіменного голосування?<div class='remove-solution'><span class='remove-solution__yes'>ТАК</span><span class='remove-solution__no'>НІ</span></div></div></div>"
      );

      $(".remove-solution__yes").on("click", function () {
       $.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            doc_id: file_id,
            filename: filename,
            num_ses: num_ses,
            convocation: convocation,
            nonce: nonce,
            action: "khor_golos_rm",
          },
          success: function (data) {
            alert(data);
            if($(e.target).closest('tbody').children().length == 1){
              $(e.target).closest('.show_upload_table').hide('slow');
            }
            $(e.target).closest('tr').hide('slow');
          },
        });
        $(this).parents(".confirm-delete-session").remove();
      });

      $(".remove-solution__no").on("click", function () {
        $(this).parents(".confirm-delete-session").remove();
      });
    },
    "click .deput-change-button": function (e, value, row, index) {
      jQuery(e.currentTarget).hide();
      jQuery(e.currentTarget).closest("tr").find(".photo-input-wrapper").show();
      jQuery(e.currentTarget)
        .closest("td")
        .children(".stack-button-active-deputy")
        .css("display", "flex");

      //let deputName = jQuery(e.currentTarget).closest("tr").find(".deput-name").attr("contenteditable", "true").addClass("active-change");
      let deputFraction = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-fraction").attr("contenteditable", "true").addClass("active-change");
      let deputConvocation = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-convocation").attr("contenteditable", "true").addClass("active-change");
      let deputBirthday = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-birthday input").attr("disabled", false);
      let deputPosition = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-position").attr("contenteditable", "true").addClass("active-change");
      let deputComission = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-comission").attr("contenteditable", "true").addClass("active-change");
      let deputInfo = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-main-info").attr("contenteditable", "true").addClass("active-change");
    },
    "click .admin_deputy-cancel-button": function (e) {
      //let deputName = jQuery(e.currentTarget).closest("tr").find(".deput-name").attr("contenteditable", "false").removeClass("active-change");
      let deputFraction = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-fraction").attr("contenteditable", "false").removeClass("active-change");
      let deputConvocation = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-convocation").attr("contenteditable", "false").removeClass("active-change");
      let deputBirthday = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-birthday input").attr("disabled", true);
      let deputPosition = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-position").attr("contenteditable", "false").removeClass("active-change");
      let deputComission = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-comission").attr("contenteditable", "false").removeClass("active-change");
      let deputInfo = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-main-info").attr("contenteditable", "false").removeClass("active-change");
      let deputImg = jQuery(e.currentTarget).closest("tr").find(".photo-input-wrapper").hide();

      jQuery(e.currentTarget).closest(".stack-button-active-deputy").hide();
      jQuery(e.currentTarget)
        .closest(".stack-button-active-deputy")
        .siblings(".deput-change-button")
        .css("display", "block");
    },
    "click .admin_deputy-save-button": (e, row) => saveButtonDeput(e, row)
  };

  function saveButtonDeput(e, row){
      let deputName = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-name")
        .text();
      let deputFraction = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-fraction")
        .text();
        let deputConvocation = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-convocation")
        .text();
      let deputBirthday = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-birthday input")
        .val();
      let deputPosition = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-position")
        .text();
      let deputComission = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-comission")
        .text();
      let deputInfo = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-main-info")
        .text();
      let deputImg = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-golos-photo")
        .attr('src');
      let deputID = jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-golos-photo").attr('id');

      /*jQuery(e.currentTarget)
        .closest("tr")
        .find(".deput-photo-upload")
        .submit();*/
      //let nonce = row._data.nonce;

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "khor_golos_admin_deputy_save_row",
          deputName: deputName,
          deputFraction: deputFraction,
          deputConvocation: deputConvocation,
          deputBirthday: deputBirthday,
          deputPosition: deputPosition,
          deputComission: deputComission,
          deputInfo: deputInfo,
          deputImg: deputImg,
          deputID: deputID,
        },
        success: function (data) {
          alert(data);
        },
      });
  }

  function uploadDeputPhoto(){
    $(".upload_image_button").click(function () {
      var send_attachment_bkp = wp.media.editor.send.attachment;
      var button = $(this);
      wp.media.editor.send.attachment = function (props, attachment) {
        $(button).parent().prev().css('display','block').attr("src", attachment.url);
        $(button).css('display','none');
        $(button).next().css('display','block');
        $(button).prev().val(attachment.url);
        wp.media.editor.send.attachment = send_attachment_bkp;
      };
      wp.media.editor.open(button);
      return false;
    });
  }

  function clearDeputPhoto(){
    $(".remove_image_button").click(function () {
      var r = confirm("Впевнені?");
      if (r == true) {
        var src = $(this).parent().prev().attr("data-src");
        $(this).css('display','none');
        $(this).prev().css('display','block');
        $(this).parent().prev().css('display','none');
        $(this).parent().prev().attr("src", src);
        $(this).prev().prev().val("");
      }
      return false;
    });
  }

  function PrintNameFileWhenLoad(){
    $(".upload-file-input").change(function () {
      if (this.files[0]) {
        if(this.files[0].type == 'application/json'){
          var icon = '../wp-content/plugins/khor-golos/assets/images/json-file.svg';
          var color = '#9777a8';
        }else if(this.files[0].type == 'application/x-zip-compressed'){
          var icon = '../wp-content/plugins/khor-golos/assets/images/zip.svg';
          var color = '#556080';
        }
        $(this).siblings(".upload-file-text").text(this.files[0].name).css('color', color);
        $(this).siblings(".upload-file-text").prepend(`<img width="64px" class="ok-type-upload-file" src="${icon}">`);
        $(this).closest(".upload-file-input-wrapper").css({
          background: '#268b4521',
          border: '3px dashed #11a022'
        });
      }
    });
  }

  function uploadSelectorShow(){
    $(".ok-head-selector-upload-file span").on("click", function(e){
      if (!$(this).hasClass('ok-active')){
        $('.ok-wrap').addClass('ok-animation-toggle');
        $(".ok-head-selector-upload-file span").removeClass('ok-active');
        $(".ok-upload_file").removeClass('ok-toggle-active');
        $(".ok-upload-result-file").removeClass('ok-toggle-active');
        $(this).toggleClass('ok-active');
        $('.' + $(this).attr('show')).toggleClass('ok-toggle-active');
      }
      setTimeout(() => $('.ok-wrap').removeClass('ok-animation-toggle'), 500);
      
    });
  }

  function MessageWhenUploadFiles(){
    $('.submit-file-upload').on("submit", function(event) {
      $.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: 'json',
        data: {
          action: "khor_golos_send_file_event",
        },
        success: function (data) {
          alert(data);
        },
      });
    });
  }

  function addNewDeput(){
    $('.ok-add-new-deput button').click((e)=>{
      $('.ok-add-new-deput-show').addClass('open');
      $(document).mouseup((e) => {
        var form = $(".ok-add-new-deput-form-group-input");
        if (!form.is(e.target) && form.has(e.target).length === 0 && $(".media-modal-content").has(e.target).length === 0) {
          form.parents('.ok-add-new-deput-show').removeClass('open').addClass('hide');
          $(document).unbind('mouseup');
        }
        if( e.target.className === 'ok-add-new-deput-form-window_submit'){
          $(e.target).parents('.ok-add-new-deput-form-group-input').find('.ok-error').remove();
          $(e.target).parents('.ok-add-new-deput-form-group-input').children().removeClass('error-yes');
          let dataNewDeput = {};
          let formInput = $(e.target).parents('.ok-add-new-deput-form-group-input').children();
          let error = [];
          console.log(error);

          let deputPhoto = $(e.target).parents('.ok-add-new-deput-form-group-input').find('.deput-golos-photo').attr('src');
          dataNewDeput.photoDeput = deputPhoto;

          for(let value of formInput){
            
            if($(value).attr('required') && $(value).val() === ''){
              $(value).next('.ok-error').remove();
              error.push({text:'Введіть необхідні данні'});
              $(value).addClass('error-yes').after(`<div class='ok-error'>Введіть необхідні данні</div>`);
            }

            const regText = /^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\),’:;«»]+$|^\s*$/g;
            let match = regText.exec($(value).val());

            if(match !== null){
        
              if($(value).prop("tagName") === 'INPUT'){
                if($(value).attr('type') === 'text' || $(value).attr('type') === 'date'){
                  
                  if($(value).attr('name').includes('deput_name')){
                    dataNewDeput.nameDeput = $(value).val();
                  }
                  if($(value).attr('name').includes('deput_fraction')){
                    dataNewDeput.fractionDeput = $(value).val();
                  }
                  if($(value).attr('name').includes('deput_convocation')){
                    dataNewDeput.convocationDeput = $(value).val();
                  }
                  if($(value).attr('name').includes('deput_birthday')){
                    const regNumber = /[0-9-]|^\s*$/g;
                    let match = regNumber.exec($(value).val());
                    if(match !== null){
                      dataNewDeput.birthdayDeput = $(value).val();
                    }else{
                      $(value).next('.ok-error').remove();
                      $(value).addClass('error-yes').after(`<div class='ok-error'>Недопустимі символи</div>`);
                    }
                  }
                  if($(value).attr('name').includes('deput_position')){
                    dataNewDeput.positionDeput = $(value).val();
                  }
                  if($(value).attr('name').includes('deput_commission')){
                    dataNewDeput.commissionDeput = $(value).val();
                  }
                }
              }
              if($(value).prop("tagName") === 'TEXTAREA'){
                if($(value).attr('name').includes('deput_info')){
                  dataNewDeput.infoDeput = $(value).val();
                }
              }
            }else{
              $(value).next('.ok-error').remove();
              error.push({text:'Недопустимі символи'});
              $(value).addClass('error-yes').after(`<div class='ok-error'>Недопустимі символи</div>`);
            }
          }

          if(error.length === 0){
            $.ajax({
              url: ajaxurl,
              type: "POST",
              dataType: "html",
              data: {
                action: "khor_golos_admin_add_new_deput",
                dataNewDeput
              },
              success: function (data) {
                console.log(data);
                $(e.target).parents('form')[0].reset();
                alert(data);
              },
            });
          }
        }
      });
    });
    
  }

  function changeSelectWhenUploadResultFile(){
    $('.golos_upload_result_field-num-convocation').on('change', function (e) {
      let optionSelected = $("option:selected", this);
      let valueSelected = this.value;

      if(valueSelected){
        $.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            action: "khor_golos_admin_change_select_upload_result_file",
            valueSelected
          },
          success: function (data) {
            $('.golos_upload_result_field-num-session').children().slice(1).remove();
            $('.golos_upload_result_field-num-session').removeAttr("disabled");
            for(let value of JSON.parse(data)){
              $('.golos_upload_result_field-num-session').
                append(`<option value="${value.allSession}">${value.allSession}</option>`);
            }
          },
        });
      }
  });
  }

  function showHideDeputTableSelector(){
    $('.ok-show-table').click((e)=>{
      $(e.target).closest('.ok-deput-table-admin').toggleClass('show');
    });
  }

  $(document).ready(function () {
    PrintNameFileWhenLoad();
    MessageWhenUploadFiles();
    uploadDeputPhoto();
    clearDeputPhoto();
    uploadSelectorShow();
    addNewDeput();
    changeSelectWhenUploadResultFile();
    showHideDeputTableSelector();
  }); // document.ready
  
  $("#khor_golos_deput_table").on("page-change.bs.table", function (e) {
    uploadDeputPhoto();
    clearDeputPhoto();
    window.operateEvents["click .admin_deputy-save-button"] = (e, row) => saveButtonDeput(e, row);
  });

})(jQuery);