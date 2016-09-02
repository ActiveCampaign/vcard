<?php
/***************************************************************************

PHP vCard class v2.0
(c) Kai Blankenhorn
www.bitfolge.de/en
kaib@bitfolge.de


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

class vCard {
	var $properties;
	var $filename;

	function setPhoneNumber($number, $type="") {
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = "TEL";
		if ($type!="") $key .= ";".$type;
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = quoted_printable_encode($number);
	}

	function setPhoto($type, $photo_url) { // $type = "GIF" | "JPEG"
		//$this->properties["PHOTO;VALUE=URL;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
		$this->properties["PHOTO;$type"] = $photo_url;
	}

	function setFormattedName($name) {
		$this->properties["FN"] = quoted_printable_encode($name);
	}

	function setName($family="", $first="", $additional="", $prefix="", $suffix="") {
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";
		$this->filename = "$first%20$family.vcf";
		if ($this->properties["FN"]=="") $this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
	}

	function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties["BDAY"] = $date;
	}

	function setAddress($name = "", $postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";
		if ($type!="") $key.= ";$type";
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = encode($name).";".encode($extended).";".encode($street).";".encode($city).";".encode($region).";".encode($zip).";".encode($country);

		//if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "") {
			//$this->setLabel($postoffice, $extended, $street, $city, $region, $zip, $country, $type);
		//}
	}

	function setLabel($postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
		$label = "";
		if ($postoffice!="") $label.= "$postoffice\r\n";
		if ($extended!="") $label.= "$extended\r\n";
		if ($street!="") $label.= "$street\r\n";
		if ($zip!="") $label.= "$zip ";
		if ($city!="") $label.= "$city\r\n";
		if ($region!="") $label.= "$region\r\n";
		if ($country!="") $country.= "$country\r\n";

		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}

	function setEmail($address) {
		$this->properties["EMAIL;INTERNET"] = $address;
	}

	function setNote($note) {
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($note);
	}

	function setURL($url, $type="") {
	// $type may be WORK | HOME
		$key = "URL";
		if ($type!="") $key.= ";$type";
		$this->properties[$key] = $url;
	}

	function getVCard() {
		$text = "BEGIN:VCARD\r\n";
		$text.= "VERSION:2.1\r\n";
		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
		}
		$text.= "REV:".date("Y-m-d")."T".date("H:i:s")."Z\r\n";
		$text.= "MAILER:PHP vCard class by Kai Blankenhorn\r\n";
		$text.= "END:VCARD\r\n";
		return $text;
	}

	function getFileName() {
		return $this->filename;
	}
}

?>