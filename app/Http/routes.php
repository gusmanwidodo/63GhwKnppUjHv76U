<?php

use App\Http\Classes\Dom;

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

	$proxies = [
		'106.184.7.132:8088',
		'109.196.127.35:8888',
		'109.69.2.125:8080',
		'120.198.233.211:8080',
		'120.198.244.29:80',
		'120.198.244.29:8081',
		'180.249.225.59:80',
		'128.199.167.223:3128'
	];

	$proxy = '106.184.7.132:8088';

	if (isset($proxies)) {
	    $proxy = $proxies[array_rand($proxies)];
	}

	set_time_limit(1000);	


		$i = File::get(storage_path('count.txt'));

		$i = (int) $i;

		if ($i == 101537) return 'finish';
    
    // for ($i=1; $i<=101537; $i++) {
    // for ($i=1; $i<=1; $i++) {

	    $page = new Dom;

	    $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

	    foreach ($page->find('.list .listingdata') as $list) {

	    	$link_dom = $list->find('.productdataimg a', 0);

	    	$link = 'http:' . $link_dom->getAttribute('href');

	    	$thumb = $list->find('.productdataimg a img', 0);

	    	$logo = 'http://m.indonetwork.co.id' . $thumb->getAttribute('src');

	    	$name = $list->find('.productdata .content h3 a', 0)->text;

	    	$address = $list->find('.productdata .companytitle h4', 0)->text;


            $company_dom = new Dom;
            $company_dom->loadFromUrlProxy($link .'/info', $proxy);

            $dict = [
                'contact_person', 'email', 'phone', 'alamat', 'kodepos', 'negara', 'provinces', 'kota', 'website', 'gabung', 'update'
            ];

            $j = 0;
            $comp = [];

            foreach($company_dom->find('.userpage .rightcontent .form .column-9') as $val){
                if($dict[$j] == 'email' || $dict[$j] == 'phone'){
                    $comp[$dict[$j]] = $val->find('a')->getAttribute('id');
                }else{
                    $comp[$dict[$j]] = $val->innerHtml();
                }
                
                $j++;
            }

	    	$company = new App\Company;
	    	$company->name = $name;
	    	$company->logo = $logo;
	    	$company->link = $link;
	    	// $company->address = $address;

            if($comp){
                $company->contact_person = strip_tags($comp['contact_person']);
                $company->email = base64_decode($comp['email']);
                $company->phone = base64_decode($comp['phone']);
                $company->address = strip_tags($comp['alamat']);
                $company->zipcode = strip_tags($comp['kodepos']);
                $company->city = strip_tags($comp['kota']);
                $company->province = strip_tags($comp['provinces']);
                $company->website = strip_tags($comp['website']);
            }else{
                $company->contact_person = '';
                $company->email = '';
                $company->phone = '';
                $company->address = '';
                $company->zipcode = '';
                $company->city = '';
                $company->province = '';
                $company->website = '';
            }

	    	$company->save();

	    }


		File::put(storage_path('count.txt'), $i+1);

    // }

    return 'success ' . File::get(storage_path('count.txt'));


});
