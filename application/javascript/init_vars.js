/*  This Array is the Structure of the main windows with folowing structure
 * key[0] => 'LayerID'
 * key[1] => 'Highlighting Element on mousover over key[0] item'
 * key[2] => Highlighting color
 * key[3] => Default Value of new textfield
 * key[4] => left distance
 * key[5] => top distance
 */
// Enter all objects here which shold get controled by input change/resetdefault
var sInputWatcher = ".mvalname, .mval, input.area, .catrename, #src, .comprename, #location, #element, #attr, #limit, .system_login";
// Lookup who called the Dialog
var sRequestSender = false;
// Datas predefined for transmit to server
var sGlobalDatas = '';
// Catch currently focused input field value
var sFocusinTmp = '';

var sPreventedSubmits = '.cat_search';
// Known primarys required for identification on startup
var aKnownPrimarys = new Array('idmeta_namevalue', 'idmeta', 'ID');


var iLastScrollCat = 0;
var scrollLockCat = false;
var scrollLockMeta = false;
var scrollLockComp = false;
var scrollLockCompMeta = false;
var iCurCatIn = 0;
var iInCat = false;
var iCatMove = false;
var iTmp = false;
var oTree = false; // holds the last loadet category tree
var oCompList = false; // Holds the last loaded complist
var iComp = false;
var oMoveMeta = false;
var iCurComp = false;
var bLockCompDuplicate = false;
// Variables required for the filters (case sensitzive / not case sensitive)
var sFilterModeCat = 'expr_cs';
// Get nr for client browser
// 0 => msie
// 1 => firefox
// 1 => Default value with transparency
var iBrowser = 1;
$.each($.browser, function(sPropName, mValue){
    iBrowser = (sPropName == 'msie') ? 0 : iBrowser;
});
// LOCKED CONSTANTS
// ID
// ELEMENTS
var aDepthMarginTop = new Array(0);
var oStageData = {};
var doDrag = {};
doDrag.action = function(event, ui){
    if (!oMoveMeta) {
        oMoveMeta = $(this).parent();
    }
    if (!$('#overflow_middle').hasClass('dragger_target_bg')) {
        $('#overflow_middle').addClass('dragger_target_bg');
    }
};
var aComps = {
    1: 'Testcomp_1',
    2: 'Testcomp2',
    3: 'Testcomp3'
};


var oSpriteDefault = new Image(); // Imageobject for default Sprite
var oSpriteHover = new Image(); // Imageobject for mouseover Sprite
var oSpriteClick = new Image(); // Imageobject for mousedown Sprite
var oSpriteActive = new Image(); // Imageobject for active Sprite
var sDefaultImage = false;
