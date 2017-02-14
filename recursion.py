#!/usr/bin/python

import urllib2
import json


response = urllib2.urlopen('https://thomas-zastrow.de/collections/api.php/collections/')
collections = json.loads(response.read())
response.close()
print "digraph G {"
for collection in collections:
	response = urllib2.urlopen('https://thomas-zastrow.de/collections/api.php/collections/' + str(collection))
	members = json.loads(response.read())
	response.close()
	for member in members:
		print collection, "->", member, ";"
print "}"
