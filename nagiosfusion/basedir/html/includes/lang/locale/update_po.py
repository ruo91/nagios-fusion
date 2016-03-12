#!/usr/bin/env python

import polib
import sys
import optparse
import os
try:
    import json
except:
    import simplejson as json
import urllib
import requests
import re
import HTMLParser
reload(sys)
sys.setdefaultencoding('utf-8')

h = HTMLParser.HTMLParser()

nefarious_regex = re.compile('shinken|icinga|op5|opsview|zabbix', re.IGNORECASE) 

def translate(text, tl, sl='en', key=None):
    
    TRANSLATE_API_ADDRESS = 'https://www.googleapis.com/language/translate/v2'
    get_params = { 'q' : text, 'source': sl, 'target': tl, 'key': key }
    
    raw_json = requests.get(TRANSLATE_API_ADDRESS, params=get_params)
    pro_json = json.loads(raw_json.text)
    
    if 'error' in pro_json:
        print pro_json
        print 'Error encountered: terminate? [y/n]'
        inp = raw_input()
        if inp == 'n':
            return None
        else:
            sys.exit(1)
    else:
        return pro_json['data']['translations'][0]['translatedText']

def clean_msgstr(msgstr):
    entities = "&.+?;"
    html_entities = re.findall(entities, msgstr)
    for entity in html_entities:
        tmp = h.unescape(entity)
        msgstr = msgstr.replace(entity, tmp)
    return msgstr

def parse_args():
    
    parser = optparse.OptionParser()
    
    parser.add_option('-p', '--pot-file', help='The original PO file.')
    parser.add_option('-l', '--locale-dir', help='Locale directory PO files are stored in.')
    parser.add_option('-k', '--google-key', help='Google API key.')
    parser.add_option('-c', '--force-create', help='Force create PO files that do not exist.', action='store_true')
    parser.add_option('-s', '--save-as-mo', help='Compile them down to MO files.', action='store_true')
    
    options, args = parser.parse_args()
    
    if not options.pot_file:
        parser.error('Must give the pot file.')
    if options.locale_dir:
        options.locale_dir = os.path.abspath(options.locale_dir)
    
    return options

def make_file_safely(po_filename):
    '''
    Should safely make the file. Should make 
    '''
    directory, filename = po_filename.rsplit('/', 1)
    os.makedirs(directory)
    open(po_filename, 'w').close()

def get_files_to_translate(lfile):
    '''
    Should get a list of files to translate based in the 
    $lang/TO_TRANSLATE directory.
    '''
    to_translate_dir = re.sub('LC_MESSAGES.*', 'TO_TRANSLATE', lfile)
    try:
        po_list = os.listdir(to_translate_dir)
    except:
        po_list = []
    return po_list

def guess_language(po_filename):
    
    _, filename = po_filename.rsplit('/', 1)
    country_code, _       = filename.rsplit('.', 1)
        
    prefix, suffix = country_code.split('_')
    prefix = prefix.lower()
    suffix = suffix.lower()
    if prefix == suffix or prefix == 'en':
        return prefix.lower()
    if prefix == 'ko':
		return prefix
    else:
        return country_code

def get_language_files(locale_dir, force):
    '''
    Should return the language directories given a localdirectory,
    and return them in a list.
    '''
    dir_contents = os.listdir(locale_dir)
    list_of_po   = []
    
    for item in dir_contents:
        full_name = '/'.join([locale_dir, item])
        if os.path.isdir(full_name):
            po_file = '/'.join([full_name, 'LC_MESSAGES', item + '.po'])
            if not os.path.isfile(po_file):
                if force:
                    make_file_safely(po_file)
                    
                else:
                    continue
            list_of_po.append(po_file)
    
    return list_of_po

def main():
    
    options = parse_args()
    language_files = get_language_files(options.locale_dir, options.force_create)
    
    pot_file = polib.pofile(options.pot_file)
    warning_file = open('warning.log', 'w')
    
    for lfile in language_files:
        to_translate = polib.pofile(lfile)
        language = guess_language(lfile)
        po_list = get_files_to_translate(lfile)
        
        if language == 'en':
            continue
        
        to_translate.merge(pot_file)
        to_translate.save()
        
        #~ Now we merge in any new files
        
        for new_trans in po_list:
            os.system('/usr/bin/msgmerge %s %s -o %s' % (new_trans, lfile, lfile))
        
        to_translate = polib.pofile(lfile)
        
        total = len(to_translate.untranslated_entries())
        count = 1
        #~ Translate with Google Translate
        print '--- Working on %s : %d entries to translate.' % (language.upper(), total)
        
        for entry in to_translate.untranslated_entries():
            entry.msgstr = translate(entry.msgid.lower(), language, key=options.google_key)
            entry.flags.append(u'fuzzy')
            print '%s [%d/%d] ...%s... --> ...%s...' % (language, count, total, entry.msgid[:10], entry.msgstr[:10])
            count += 1
        #~ Clean up msgstr entries
        total = len(to_translate)
        count = 1
        for entry in to_translate:
            tmp_msgstr = entry.msgstr
            entry.msgstr = clean_msgstr(entry.msgstr)
            if tmp_msgstr != entry.msgstr:
                print 'Cleaned: %s --> %s' % (tmp_msgstr, entry.msgstr)
            if nefarious_regex.search(entry.msgstr):
                match = nefarious_regex.match(entry.msgstr).group(1)
                print 'Warning: Found improper match %s.' % match
                warning_log.write('Warning: Found improper match %s in %s' % (match, entry.msgstr))
        
        to_translate.save()
        
        if options.save_as_mo:
            filename, suffix = lfile.rsplit('.', 1)
            mo_file = filename + '.mo'
            os.system('/usr/bin/rm %s -f' % mo_file)
            print 'Saving mo as %s' % mo_file
            os.system('msgfmt -o %s --use-fuzzy %s' % (mo_file, filename))
    
if __name__ == '__main__':
    main()
