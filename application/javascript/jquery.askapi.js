/**
 * Plugin for send requests to {our software}
 *
 *
 * @author	  Bernhard Bezdek
 * @copyright Bernhard Bezdek / Dominik Kloke (bernhard.bezdek@googlemail.com)
 * @license   GPL
 * @link	  http://www.dsbrg.net/api
 *
 * @version   0.9.0
 *
 * Created:   2011-08-05  Bernhard Bezdek
 *
 * HOW TO USE:
 * You are allowed to call public data defined in your or foreign systems
 * Defined Namespaces:
 * - users
 * - metas
 * - categories
 * - composites
 *
 * // USER REQUEST EXAMPLE
 * {
 * 	error:errorFunction 			// Function called on error (occurred error will be assigned)
 *  success:successFunction 		// Function called after successful apicall (Response object will be assigned)
 * }
 * // META REQUEST EXAMPLES
 *
 * // CATEGORY REQUEST EXAMPLES
 *
 * // COMPOSITE REQUEST EXAMPLE
 *
 *
 * !!!!!!!!!!!! NOTE WE ARE IN BETA STATE !!!!!!!!!!!!
 * please send us an email for further informations about changes on api
 *
 */
(function($) {
	$.fn.askAPI = function(oReq, callbackFunction) {
		var sApiBase = 'http://dsbrg.net/api/';
		var sJsonCallback = '?format=jsonp&jsoncallback=?';
		var oNS = {};
		oNS.users = 'users';
		oNS.metas = 'metas';
		oNS.categories = 'categories';
		oNS.composites = 'composites';

		// Internal functions
		// Check if given string is an existing function
		function isFn(sFunction) {
			if(typeof(sFunction) == 'function') {
				return true;
			}
			return false;
		}

		function isStr(sStr) {
			if(typeof(sStr)=='string') {
				return true;
			}
			return false;
		}

		if(isFn(oReq.success)) {
			// LOOKUP WHICH NAMESPACE SHOPULD BE ASKED
			var sReqStr = false;
			if(oReq.namespace==oNS.categories || oReq.namespace==oNS.composites || oReq.namespace==oNS.metas || oReq.namespace==oNS.users) {
				if(oReq.namespace==oNS.categories) {
					if(isStr(oReq.who)) {
						sReqStr = oNS.categories+'/'+oReq.who+'/';
						if(isStr(oReq.childsOf)) {
							sReqStr += 'subcategories/'+oReq.childsOf+'/';
						}
					} else {
						console.log('No user selected');
					}
				}

				if(oReq.namespace==oNS.composites) {
					if(isStr(oReq.who)) {
						sReqStr = oNS.composites+'/'+oReq.who+'/';
						if(typeof(oReq.idComposite)!='undefined') {
							sReqStr += oReq.idComposite+'/';
						} else {
							// ASK WITH METAS
							if(typeof(oReq.withMeta)!='undefined') {
								sReqStr += 'meta/'+oReq.withMeta+'/';
								if(isStr(oReq.withProperty)) {
									sReqStr += oReq.withProperty+'/';
									if(isStr(oReq.withValue)) {
										sReqStr+=oReq.withValue+'/';
									}
								}
							} else {
								if(isStr(oReq.withProperty)) {
									sReqStr += oReq.withProperty+'/';
									if(isStr(oReq.withValue)) {
										sReqStr+=oReq.withValue+'/';
									}
								}
							}
						}
					} else {
						console.log('No user selected');
					}
				}

				if(oReq.namespace==oNS.metas) {
					if(isStr(oReq.who)) {
						sReqStr = oNS.metas+'/'+oReq.who+'/';
						if(isStr(oReq.withProperty)) {
							sReqStr += oReq.withProperty+'/';
							if(isStr(oReq.withValue)) {
								sReqStr+=oReq.withValue+'/';
							}
						}
					} else {
						console.log('No user selected');
					}
				}

				if(oReq.namespace==oNS.users) {
					sReqStr = 'users/';
				}

				if(sReqStr) {
					$.getJSON(sApiBase+sReqStr+sJsonCallback, function(data) {
						if(typeof(data.error)=='undefined') {
							oReq.success(data);
						} else {
							if(isFn(oReq.error)) {
								oReq.error(data.error);
							}
						}
					});
				} else {
					console.log('Invalid Request (Don\'t ask API)');
				}

			} else {
				console.log('Namespace "'+oReq.namespace+'" is not defined');
			}
		} else {
			console.log('Callback function ('+oReq.success+') wasn\'t found');
		}
	}
})(jQuery);