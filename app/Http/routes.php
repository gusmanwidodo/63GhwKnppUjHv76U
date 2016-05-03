<?php

use PHPHtmlParser\Dom;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/category', function () {
    
    $dom = new Dom;

    $dom->load('http://m.indonetwork.co.id/categories');

    foreach ($dom->find('.kategori ul li a') as $a) {

    	$category = new App\Category;
    	$category->name = $a->text;
    	$category->save();

    	$subcat = new Dom;
	 	
	 	$subcat->load($a->getAttribute('href'));

	 	foreach ($subcat->find('.kategori ul li a') as $aa) {

	 		if ($aa->text != '') {

		    	$category2 = new App\Category;
		    	$category2->name = $aa->text;
		    	$category2->parent_id = $category->id;
		    	$category2->link = $aa->getAttribute('href');
		    	$category2->save();

		    }

	 	}

    }

    return 'success';


});

Route::get('/company', function () {

	set_time_limit(1000);
    
    // for ($i=1; $i<=101537; $i++) {
    for ($i=1; $i<=1; $i++) {

	    $page = new Dom;

	    $page->load('http://m.indonetwork.co.id/search?type=company&page=' . $i);

	    foreach ($page->find('.list .listingdata') as $list) {

	    	$link = 'http:' . $list->find('.productdataimg a', 0)->getAttribute('href');

	    	$thumb = $list->find('.productdataimg a img', 0);

	    	$logo = 'http://m.indonetwork.co.id' . $thumb->getAttribute('src');

	    	$name = $list->find('.productdata .content h3 a', 0)->text;

	    	$address = $list->find('.productdata .companytitle h4', 0)->text;


	    	$company = new App\Company;
	    	$company->name = $name;
	    	$company->logo = $logo;
	    	$company->link = $link;
	    	$company->address = $address;

	    	$company->save();

	    }

    }

    return 'success';


});
