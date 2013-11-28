/**
 * This file is a base preconfirguration for defined areas
 * Each numerical array is the representation of an area
 * Reulary Cats, Comp, Meta, Groups which will be configured on startup
 *
 *
 * Description:
 *
 * The numerical arrays subarray properties are use as follow
 *
 * layers[0][0] => Target Id for following properties
 * layers[0][1] => Highlighting target by mouse over Area
 * layers[0][2] => Highlighting Color of previeous (layers[0][1]) target
 * layers[0][3] => Input field default value
 * layers[0][4] => Init position left
 * layers[0][5] => Init position top
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 */
// Areas/Areapropertys
var highlightColor = new Array();
highlightColor[0] = new Array('#f00', 'rgba(0, 255, 32, 0.4)');
highlightColor[1] = new Array('#0f0', 'rgba(0, 255, 32, 0.4)');
highlightColor[2] = new Array('#00f', 'rgba(0, 255, 32, 0.4)');
highlightColor[3] = new Array('#ff0', 'rgba(255, 134, 74, 0.6)');

var layers = new Array();
layers[0] = new Array('#cats', '#comp .head', highlightColor[0][iBrowser], 'new Category', '20px', '10px');
layers[1] = new Array('#comp', '#meta .items', highlightColor[1][iBrowser], 'new Composite', '332', '10px');
layers[2] = new Array('#meta', '#comp .items', highlightColor[2][iBrowser], 'new Metaitem', '644px', '10px');
layers[3] = new Array('#groups', '#meta .items, #comp .items', highlightColor[3][iBrowser], "new Group", '956px', '10px');

var sSpriteConf = '"scroll_down": {"type":"class","xpos": "0","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"complist_close": {"type":"class","xpos": "0","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"scroll_up": {"type":"class","xpos": "-25","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"save": {"type":"class","xpos": "-50","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"go_up": {"type":"class","xpos": "-75","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"cut": {"type":"class","xpos": "-100","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"add_cat": {"type":"class","xpos": "-125","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"delete": {"type":"class","xpos": "-150","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"edit": {"type":"class","xpos": "-175","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"paste": {"type":"class","xpos": "-200","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"add": {"type":"class","xpos": "-230","ypos": "-2","width": "25","height": "25" },';
sSpriteConf += '"tidyDesk": {"type":"class","xpos": "-256","ypos": "-2","width": "25","height": "25" },';
sSpriteConf += '"save_comp": {"type":"class","xpos": "-280","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"restore_comp": {"type":"class","xpos": "-305","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"down": {"type":"class","xpos": "-6","ypos": "-281","width": "36","height": "80" },';
sSpriteConf += '"up": {"type":"class","xpos": "-52","ypos": "-281","width": "36","height": "80" },';
sSpriteConf += '"confirm": {"type":"class","xpos": "-4","ypos": "-240","width": "185","height": "30" },';
sSpriteConf += '"abort": {"type":"class","xpos": "-190","ypos": "-240","width": "185","height": "30" },';
sSpriteConf += '"cat_delete": {"type":"class","xpos": "-5","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"comp_delete": {"type":"class","xpos": "-5","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"cat_infos": {"type":"class","xpos": "-36","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"comp_infos": {"type":"class","xpos": "-36","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"cat_comps": {"type":"class","xpos": "-66","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"cat_rename": {"type":"class","xpos": "-96","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"comp_rename": {"type":"class","xpos": "-96","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"comp_move": {"type":"class","xpos": "-126","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"comp_duplicate": {"type":"class","xpos": "-157","ypos": "-431","width": "19","height": "9" },';
sSpriteConf += '"complist_close": {"type":"class","xpos": "-330","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"close_comp": {"type":"class","xpos": "-355","ypos": "0","width": "25","height": "25" },';
sSpriteConf += '"comp_open": {"type":"class","xpos": "-380","ypos": "0","width": "26","height": "26" },';
sSpriteConf += '"insert_comp": {"type":"class","xpos": "-125","ypos": "0","width": "25","height": "25" }';
