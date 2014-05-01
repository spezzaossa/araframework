//Resources:
//    http://de.wikipedia.org/wiki/ROT13
//    http://www.drweb.de/magazin/codieren-und-verschlusseln-mit-javascript/
//
////////////////////////////////////////////////
//(C) 2010 Andreas  Spindler. Permission to use, copy,  modify, and distribute
//this software and  its documentation for any purpose with  or without fee is
//hereby  granted.   Redistributions of  source  code  must  retain the  above
//copyright notice and the following disclaimer.
//
//THE SOFTWARE  IS PROVIDED  "AS IS" AND  THE AUTHOR DISCLAIMS  ALL WARRANTIES
//WITH  REGARD   TO  THIS  SOFTWARE   INCLUDING  ALL  IMPLIED   WARRANTIES  OF
//MERCHANTABILITY AND FITNESS.  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY
//SPECIAL,  DIRECT,   INDIRECT,  OR  CONSEQUENTIAL  DAMAGES   OR  ANY  DAMAGES
//WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION
//OF  CONTRACT, NEGLIGENCE  OR OTHER  TORTIOUS ACTION,  ARISING OUT  OF  OR IN
//CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
//
//$Writestamp: 2010-06-09 13:07:07$
//$Maintained at: www.visualco.de$

function ROTn(text, map) {
	// Generic ROT-n algorithm for keycodes in MAP.
	var R = new String()
	var i, j, c, len = map.length
	for(i = 0; i < text.length; i++) {
		c = text.charAt(i)
		j = map.indexOf(c)
		if (j >= 0) {
		c = map.charAt((j + len / 2) % len)
		}
		R = R + c
	}
	return R;
}

function ROT47(text) {
	// Hides all ASCII-characters from 33 ("!") to 126 ("~").  Hence can be used
	// to obfuscate virtually any text, including URLs and emails.
	var R = new String()
	R = ROTn(text,"!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~")
	return R;
}

//Funzione per bloccare la propagazione di un evento
function stopEvent(e) {
    if(!e) var e = window.event;
    e.cancelBubble = true;
    e.returnValue = false;
    if (e.stopPropagation) {
        e.stopPropagation();
        e.preventDefault();
    }
    return false;
}

jQuery.fn.exists = function(){return this.length>0;};

