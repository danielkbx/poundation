# About
Poundation is a lightweight PHP framework. It provides a set of foundation classes e.g. String, Sets and so on. Inspired by Apple's Foundation framework, it is supposed to help with often occuring tasks and problems.
Though all poundation classes declare a namespace 'Poundation' a class Prefex __P__ (without underscore) is used. This is only to avoid syntax collisions, e.g. array <=> PArray.

# Requirements
* PHP >= 5.3
* Knowledge of object-orientated programming

# Installation
## Method 1: Shared
Place the sources in a directory that is part of your include_path directive, e.g. /usr/share/php on Debian systems.
## Method 2: Project
Place the sources in the document root of your projects.

Once the source files are at the desired location, include (or require) Poundation.php in your bootstrap file or whereever you wish to use poundation classes.

# Usage
Poundation classes are normal PHP classes so you can treat them as usual. All classes are subclasses of __PObject__. You can use the same class as superclass for your own classes.

# 1. Collections
All collection classes hold other values/objects. There work like normal PHP arrays so you can use the array syntax 

	$collection[] = 'some text';
	foreach($collection as $value) {
		echo $value;
	}

There are different types of collections:

## 1.1 PSet
A set can contain a single object only once. In details, adding the same object twice results in adding it only once.
Sets are numeric, so you cannot add an object with a key.

## 1.2 PArray
PArray manages a collection of objects identified by an index value. It is comparable to PHP's numeric array.

## 1.3 PDictionary
A dictionary associates a value with a key the same way like PHP's assioative arrays. You cannot add a value without a key. Despite this fact, dictionary behave the same way any other poundation collection class does.

# 2. PString
A string object wraps a simple native string into a powerful object. It has a bunch of methods to manipulate and alter the string.
