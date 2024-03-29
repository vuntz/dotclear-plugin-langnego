<?php
# ***** BEGIN LICENSE BLOCK *****
# Copyright (c) 2007 Vincent Untz
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
if (!defined('DC_RC_PATH')) return;

dcCore::app()->addBehavior('publicPrependV2',array('langNego','publicPrependV2'));

class langNego
{
        public static function publicPrependV2()
        {
		$locale_dir = path::real(DC_ROOT . '/locales/');
		if (!is_dir($locale_dir)) {
			return;
		}

		$lang = self::get_language();
		if (dcCore::app()->lang == $lang) {
			return;
		}

		l10n::lang($lang);

		/* reset all translations, since we might need to go back to
		 * english */
		$GLOBALS['__l10n'] = array();
		$GLOBALS['__l10n_files'] = array();

		l10n::set($locale_dir.'/'.$lang.'/main');
		l10n::set($locale_dir.'/'.$lang.'/date');
		l10n::set($locale_dir.'/'.$lang.'/plugins');
		l10n::set($locale_dir.'/'.$lang.'/public');

		foreach (dcCore::app()->plugins->getModules() as $id => $m) {
			dcCore::app()->plugins->loadModuleL10N($id,$lang,'main');
		}

		dcCore::app()->themes->loadModuleL10N(dcCore::app()->blog->settings->system->theme,$lang,'main');
	}

	/* Based on: http://www-128.ibm.com/developerworks/web/library/wa-apac.html */
	private static function get_language()
	{
		$known_languages = array("en", "fr");
		$language_default = "en";

		/* Format of the HTTP header:
		 * Accept-Language: fr; q=1.0, en; q=0.5 */
		$http_languages = "";
		if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
			$http_languages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		if ($http_languages == "") {
			/* no preference set */
			return $language_default;
		}

		/* form an array of preferred languages */
		$accept_language = str_replace(" ", "", $http_languages);
		$languages = explode(",", $accept_language);

		/* check for a recognized language */
		for ($i = 0; $i < sizeof($languages); $i++) {
			$pref = explode(";", $languages[$i]);
			if (in_array ($pref[0],$known_languages)) {
				/* found a preferred language */
				return $pref[0];
			}
		}

		return $language_default;
	}
}
?>
