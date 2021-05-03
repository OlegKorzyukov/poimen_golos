function detailFormatter(index, row) {
  let num_ses = row._data.session;
  let num_golos = row._data.golos;
  let num_convocation = row._data.convocation;
  let dataId = row._data.id;

  jQuery.ajax({
    url: khor_golos_ajax.url,
    type: "POST",
    dataType: "html",
    data: {
      action: "khor_golos_subtable_repeat_question",
      data_session: num_ses,
      data_id: dataId,
      data_num_golos: num_golos,
      data_convocation: num_convocation
    },
    success: function (data) {
      $('.khor_golos_table .detail-view').css('background', '#eeeeee94');
          jQuery('.main_row_session[data-index="' + index + '"]')
        .next(".detail-view")
        .children("td")
        .append(data);

        viewFileGolosPublic();
        showMetaInfoForClickPublic();
    },
  });
}

function viewFileGolosPublic() {
  $(".main_row_session .name_session .khor_golos_download_file").on(
    "click",
    function (event) {

      let link = jQuery(this).attr("link");
      if (link === "") {
        alert("Немає даних для відображення");
      } else {
        let regUrl = /:\/\/(.[^/]+)/;
        let regId = /d\/([^/]+)/;
        let urlFile = link.match(regUrl)[1];

        if (urlFile == "drive.google.com") {
          let idFile = link.match(regId)[1];
          jQuery(".khor-golos-public-table-wrapper").append(
            "<div class='view-golos-file-modal__public'><div class='modal-file-view-wrapper'><span class='close-modal-file-view'><i class='fas fa-times'></i></span><iframe src='https://drive.google.com/file/d/" +
            idFile +
            "/preview'></iframe></div></div>"
          );
        } else {
          jQuery(".khor-golos-public-table-wrapper").append(
            "<div class='view-golos-file-modal__public'><div class='modal-file-view-wrapper'><span class='close-modal-file-view'><i class='fas fa-times'></i></span><iframe id='docFrame' src='https://docs.google.com/viewer?url=" +
            link +
            "&embedded=true' style='width:100%; height:100%;' frameborder='0'></iframe></div></div>"
          );
          $("#docFrame").on("load", function () {
            $(this).addClass("golosDocIframe");
          });
          if (!$("#docFrame").hasClass("golosDocIframe")) {
            let timerId = setInterval(fixReloadIFrame, 2000);
          }
        }
      }
      clickCloseButton(".view-golos-file-modal__public");
    }
  );
}

function fixReloadIFrame() {
  $("#docFrame").on("load", function () {
    $(this).addClass("golosDocIframe");
  });
  if (!$("#docFrame").hasClass("golosDocIframe")) {
    console.log("try to load");
    let iframe = $(".golosDocIframe")[0];
    $(iframe).attr("src", $(iframe).attr("src"));
  }
}

function clickCloseButton(parentClass) {
  jQuery(".close-modal-file-view").on("click", function () {
    jQuery(this).parents(parentClass).remove();
  });
}

function showMetaInfoForClickPublic() {
  jQuery(".result_session .result_session_main").on("click", function (e) {
    let titleModalWindow = $(this)
      .closest(".main_row_session")
      .find(".name_session span")
      .text();
    let dateTitleModalWindow = $(this)
      .closest(".main_row_session")
      .find(".time_session")
      .text();
    let dataSend = {
      data_session: $(this)
        .closest(".main_row_session")
        .attr("data-session"),
      data_convocation: $(this)
        .closest(".main_row_session")
        .attr("data-convocation"),
      data_pdname: $(this).closest(".main_row_session").attr("data-golos"),
      data_glnum: $(this).closest(".main_row_session").attr("data-glnum"),
    };
    let nonceField = $(this).parents(".khor-golos-public-table-wrapper").attr('data-nonce');

    let clickTarget = e.target.closest(".row");

    let ajaxQueryConstructor = function (actionType) {
      jQuery.ajax({
        url: khor_golos_ajax.url,
        type: "POST",
        data: {
          dataSend: dataSend,
          nonce: nonceField,
          action: actionType,
          sendType: "khor_golos_public_meta_table_ajax_click_action",
        },
        success: function (data) {
          let decodeJson = JSON.parse(data);
          jQuery(".khor-golos-public-table-wrapper").append(
            `<div class='view-public-golos-meta-table'>` +
            `<div class='view-public-golos-meta-table__show'>` +
            `<span class='close-modal-file-view'><i class='fas fa-times'></i></span>` +
            `<div class='view-public-golos-meta-table__title'>${titleModalWindow}</div>` +
            `<div class='view-public-golos-meta-table__title-info'>` +
            `<div><span>Сесія №: ${dataSend.data_session}</span><br/>` +
            `<span>Скликання №: ${dataSend.data_convocation}</span></div>` +
            `<span>Дата: ${dateTitleModalWindow}</span>` +
            `</div>` +
            `<div class='print-meta-table-wrapper'><a class="print-meta-table">Роздрукувати</a></div>` +
            `</div>` +
            `</div>`
          );
          for (let i = 0; i < decodeJson.length; i++) {
            jQuery(".view-public-golos-meta-table__show").append(
              `<div class='row view-public-golos-meta-table__row'><div class='col-1'>${
                  i + 1
                }</div><div class='col-7 view-public-golos-meta-table__deput'>${
                  decodeJson[i].ok_dp_name
                }</div><div class='col-4 view-public-golos-meta-table__result'>${
                  decodeJson[i].ok_dp_golos
                }</div></div></div>`
            );
          }
          printMetaTable();
          clickCloseButton(".view-public-golos-meta-table");
        },
      });
    };

    if (this.contains(clickTarget)) {
      let attrClickTarget = $(e.target).closest(".row").attr("data-golos");
      let attrClickTargetCheckEmpty = $(e.target)
        .closest(".row")
        .attr("data-empty");
      let actionType;

      switch (attrClickTarget) {
        case "golos-yes":
          actionType = "public_table_meta_golos-yes";
          break;
        case "golos-no":
          actionType = "public_table_meta_golos-no";
          break;
        case "golos-ng":
          actionType = "public_table_meta_golos-ng";
          break;
        case "golos-utr":
          actionType = "public_table_meta_golos-utr";
          break;
        case "golos-all":
          actionType = "public_table_meta_golos-all";
          break;
        default:
          actionType = false;
          break;
      }
      if (attrClickTargetCheckEmpty != 1 && actionType != false) {
        ajaxQueryConstructor(actionType);
      }
    } else {
      alert("Помилка відображення інформації голосування");
    }
  });
}

function printMetaTable() {
  $(".view-public-golos-meta-table__show .print-meta-table").on(
    "click",
    function () {
      $("body").addClass("printSelected");
      $("body").append('<div class="printSelection"></div>');
      $(this).css("display", "none");
      $(".printSelected .close-modal-file-view").css("display", "none");
      $(".printSelected .view-public-golos-meta-table__show").css({
        position: "relative",
        width: "100%",
        height: "auto",
      });
      $(".view-public-golos-meta-table__show")
        .clone()
        .appendTo(".printSelection");

      window.print();
      $(".printSelected .close-modal-file-view").css("display", "block");
      $(this).css("display", "inline");
      $(".printSelected .view-public-golos-meta-table__show").css({
        position: "fixed",
        width: "50%",
        height: "88%",
      });
      $("body").removeClass("printSelected");
      $(".printSelection").remove();
    }
  );
}



(function ($) {
  "use strict";

  function addPrintButtonTable() {
    $(".khor_golos_table").ready(function (e) {
      $(".bootstrap-table")
        .children(".fixed-table-pagination")
        .eq(1)
        .addClass("footerPagination");
      $(".bootstrap-table .columns-right").append(
        `<button class="btn btn-secondary print-table-button" type="button" name="printButton" aria-label="Print Table" title="Друкувати таблицю"><i class="fas fa-print"></i></button>`
      );
      printTable();
    });
  }

  function printTable() {
    $(".print-table-button").click(function () {
      $("body").addClass("printSelected");
      $("body").append('<div class="printSelection"></div>');
      $(".printSelected .khor_golos_download_file").css("display", "none");
      $(".printSelected .bootstrap-table .loading-text").css(
        "display",
        "none"
      ); //this text show whill print, hide this
      $(".khor_golos_table").clone().appendTo(".printSelection");

      window.print();
      $(".printSelected .khor_golos_download_file").css("display", "block");
      $(".printSelected .bootstrap-table .loading-text").css(
        "display",
        "block"
      );
      $("body").removeClass("printSelected");
      $(".printSelection").remove();
    });
  }

  function changeFooterPagination() {
    $(".khor_golos_table").ready(function (e) {
      $(".bootstrap-table")
        .children(".fixed-table-pagination")
        .eq(1)
        .addClass("footerPagination");
    });
  }

  function fixTranslateButtonTitle() {
    $(".khor_golos_table").ready(function (e) {
      $('.bootstrap-table button[name*="paginationSwitch"]').attr(
        "title",
        "Показати/Сховати всю таблицю"
      );
      $('.bootstrap-table button[aria-label*="Export"]').attr(
        "title",
        "Експортувати дані"
      );
    });
  }

  function animatePlayVideo(){
    $('.ok-public-video-wrapper a').mouseover(function(){
      $(this).find('.ok-svg-a').addClass('ok-animate-play-outline__hover');
      $(this).find('.ok-svg-b').addClass('ok-animate-play-inline__hover');
    });
    $('.ok-public-video-wrapper a').mouseout(function(){
      $(this).find('.ok-svg-a').removeClass('ok-animate-play-outline__hover');
      $(this).find('.ok-svg-b').removeClass('ok-animate-play-inline__hover');
    });
    $('.ok-public-video-wrapper a').click(function(e){
      e.preventDefault();
      $(this).find('.ok-video-wrapper__bg').addClass('ok-animate-bg__click');
      $(this).find('span').addClass('ok-animate-text__click');
      $('.ok-public-video-wrapper svg').css('margin-right', 0);

      let linkVideo = $(this).attr('href');
      let regExp = /^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/;
      let match = linkVideo.match(regExp);
      if(match){
        let videoDomain = match[3];
        if(videoDomain == 'youtube.com' || videoDomain == 'youtu.be'){
          let videoID = match[5];
        
            $('.ok-public-video-wrapper .ok-input-video').prepend(`<iframe src="https://www.youtube.com/embed/${videoID}" frameborder="0" rel="0" showinfo="0" autoplay="1" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`);
            $(this).css('position','absolute');
            $(this).siblings('.ok-input-video').css({
              height: '275px',
              position: 'relative'
            });
            setTimeout(() =>{
              $('.ok-input-video iframe').addClass('ok-remove-border-radius-video');
              setTimeout(() =>{
                $('.ok-remove-border-radius-video').css({
                  width: '100%',
                  transition: '0.5s',
                });
                $(this).hide();
              }, 400);
            }, 1200);
        }
      }

    });
  }

 
  /* -------------- Iframe some times return status 204 when load ------------- */


  function hidePlusSubrowIcon(){
    $('.khor_golos_table .without-subrow .detail-icon').closest('td').hide();
    $('.khor_golos_table .without-subrow .id_session').attr('colspan',2).css('text-align','center');
  }

  $(".khor_golos_table").on("page-change.bs.table", function (e) {
    /* -------- If change page in table function click and other not work ------- */
    showMetaInfoForClickPublic();
    viewFileGolosPublic();
    hidePlusSubrowIcon();
  });

  $(document).ready(function () {
    
    animatePlayVideo();

    showMetaInfoForClickPublic();

    addPrintButtonTable();

    viewFileGolosPublic();

    hidePlusSubrowIcon();
    /* ------------------- Hover button table translate title ------------------- */
    fixTranslateButtonTitle();

  }); // document.ready
})(jQuery);