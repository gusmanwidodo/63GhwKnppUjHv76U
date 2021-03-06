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

$proxies = [
    '115.146.123.219:8080',
    '114.41.61.82:8998',
    '45.63.22.233:80',
    '58.9.99.41:3128',
    '58.11.33.143:8888',
    '185.34.20.117:8080',
    '189.28.166.79:80',
    '212.38.166.231:443',
    '119.236.50.175:8088',
    '213.136.89.121:80',
    '36.238.130.248:8088',
    '185.86.148.106:14236',
    '114.38.93.238:8088',
    '81.169.145.246:80',
    '58.11.11.191:8888',
    '124.122.5.239:8888',
    '112.104.3.177:8888'
];

$proxy = '112.104.3.177:8888';


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

Route::get('/company', function () use ($proxies, $proxy) {

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

            if ($link_dom) {

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

	    }


		File::put(storage_path('count.txt'), $i+1);

    // }

    return 'success ' . File::get(storage_path('count.txt'));


});

Route::get('/company2', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count2.txt'));

        $i = (int) $i;

        if ($i == 50000) return 'finish';
    
    // for ($i=1; $i<=101537; $i++) {
    // for ($i=1; $i<=1; $i++) {

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count2.txt'), $i-1);

    // }

    return 'success ' . File::get(storage_path('count2.txt'));


});

Route::get('/company3', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count3.txt'));

        $i = (int) $i;
    
    // for ($i=1; $i<=101537; $i++) {
    // for ($i=1; $i<=1; $i++) {

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count3.txt'), $i-1);

    // }

    return 'success ' . File::get(storage_path('count3.txt'));


});

Route::get('/company4', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count4.txt'));

        $i = (int) $i;

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count4.txt'), $i+1);

    return 'success ' . File::get(storage_path('count4.txt'));


});

Route::get('/company5', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count5.txt'));

        $i = (int) $i;

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count5.txt'), $i+1);

    return 'success ' . File::get(storage_path('count5.txt'));


});

Route::get('/company6', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count6.txt'));

        $i = (int) $i;
    
    // for ($i=1; $i<=101537; $i++) {
    // for ($i=1; $i<=1; $i++) {

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count6.txt'), $i-1);

    // }

    return 'success ' . File::get(storage_path('count6.txt'));


});

Route::get('/company7', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count7.txt'));

        $i = (int) $i;

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count7.txt'), $i+1);

    return 'success ' . File::get(storage_path('count7.txt'));


});

Route::get('/company8', function () use ($proxies, $proxy) {

    if (isset($proxies)) {
        $proxy = $proxies[array_rand($proxies)];
    }

    set_time_limit(1000);   


        $i = File::get(storage_path('count8.txt'));

        $i = (int) $i;
    
    // for ($i=1; $i<=101537; $i++) {
    // for ($i=1; $i<=1; $i++) {

        $page = new Dom;

        $page->loadFromUrlProxy('http://m.indonetwork.co.id/search?type=company&page=' . $i, $proxy);

        foreach ($page->find('.list .listingdata') as $list) {

            $link_dom = $list->find('.productdataimg a', 0);

            if ($link_dom) {

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

        }


        File::put(storage_path('count8.txt'), $i-1);

    // }

    return 'success ' . File::get(storage_path('count8.txt'));


});

Route::get('/product', function() use ($proxies, $proxy) {

    $id = File::get(storage_path('category.txt'));

    $id = (int) $id;

    $category = App\Category::where('id', $id)->where('parent_id', '!=', 0)->orderBy('id', 'desc')->first();

    if ($category) {

        if (isset($proxies)) {
            $proxy = $proxies[array_rand($proxies)];
        }

        set_time_limit(10000);

        $page = new Dom;

        $page->loadFromUrlProxy($category->link . '?page=7', $proxy);

        foreach ($page->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

        $page2 = new Dom;

        $page2->loadFromUrlProxy($category->link . '?page=8', $proxy);

        foreach ($page2->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

    }

    File::put(storage_path('category.txt'), $id-1);

    return 'success ' . File::get(storage_path('category.txt'));

});

Route::get('/product2', function() use ($proxies, $proxy) {

    $id = File::get(storage_path('category2.txt'));

    $id = (int) $id;

    $category = App\Category::where('id', $id)->where('parent_id', '!=', 0)->orderBy('id', 'desc')->first();

    if ($category) {

        if (isset($proxies)) {
            $proxy = $proxies[array_rand($proxies)];
        }

        set_time_limit(10000);

        $page = new Dom;

        $page->loadFromUrlProxy($category->link . '?page=7', $proxy);

        foreach ($page->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

        $page2 = new Dom;

        $page2->loadFromUrlProxy($category->link . '?page=8', $proxy);

        foreach ($page2->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

    }

    File::put(storage_path('category2.txt'), $id+1);

    return 'success ' . File::get(storage_path('category2.txt'));

});

Route::get('/product3', function() use ($proxies, $proxy) {

    $id = File::get(storage_path('category3.txt'));

    $id = (int) $id;

    $category = App\Category::where('id', $id)->where('parent_id', '!=', 0)->orderBy('id', 'desc')->first();

    if ($category) {

        if (isset($proxies)) {
            $proxy = $proxies[array_rand($proxies)];
        }

        set_time_limit(10000);

        $page = new Dom;

        $page->loadFromUrlProxy($category->link . '?page=9', $proxy);

        foreach ($page->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

        $page2 = new Dom;

        $page2->loadFromUrlProxy($category->link . '?page=10', $proxy);

        foreach ($page2->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

    }

    File::put(storage_path('category3.txt'), $id-1);

    return 'success ' . File::get(storage_path('category3.txt'));

});

Route::get('/product4', function() use ($proxies, $proxy) {

    $id = File::get(storage_path('category4.txt'));

    $id = (int) $id;

    $category = App\Category::where('id', $id)->where('parent_id', '!=', 0)->orderBy('id', 'desc')->first();

    if ($category) {

        if (isset($proxies)) {
            $proxy = $proxies[array_rand($proxies)];
        }

        set_time_limit(10000);

        $page = new Dom;

        $page->loadFromUrlProxy($category->link . '?page=9', $proxy);

        foreach ($page->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

        $page2 = new Dom;

        $page2->loadFromUrlProxy($category->link . '?page=10', $proxy);

        foreach ($page2->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

    }

    File::put(storage_path('category4.txt'), $id+1);

    return 'success ' . File::get(storage_path('category4.txt'));

});

Route::get('/product2-17', function() use ($proxies, $proxy) {

    $id = File::get(storage_path('category2_17.txt'));

    $id = (int) $id;

    if ($id == 51) return 'finish';

    $category = App\Category::where('id', $id)->where('parent_id', '!=', 0)->orderBy('id', 'desc')->first();

    if ($category) {

        if (isset($proxies)) {
            $proxy = $proxies[array_rand($proxies)];
        }

        set_time_limit(10000);

        $page = new Dom;

        $page->loadFromUrlProxy($category->link . '?page=1', $proxy);

        foreach ($page->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

        $page2 = new Dom;

        $page2->loadFromUrlProxy($category->link . '?page=2', $proxy);

        foreach ($page2->find('.list .listingdata') as $list) {
            
            $link_dom = $list->find('.productdata .content p a', 0);

            if ($link_dom) {

                $link = 'http:' . $link_dom->getAttribute('href');

                $product_dom = new Dom;

                $product_dom->loadFromUrlProxy($link, $proxy);

                $cart_button = $product_dom->find('#cartData', 0);

                $desc = $product_dom->find('.descproduk', 0);

                if ($desc) {
                    $description = strip_tags($desc->innerHtml);
                } else {
                    $description = '';
                }

                if ($cart_button) {

                    $json_data = $cart_button->getAttribute('data-product');

                    $d = ($json_data) ? $json_data : '';

                    $product_info = (array) json_decode($d);

                } else {

                    $product_info = [
                        'product_name' => '',
                        'price' => '',
                        'unit' => '',
                        'qty' => '',
                        'product_img' => '',
                        'company_name' => '',
                        'company_url' => ''
                    ];
                }

                if ($product_info) {

                    $product = new App\Product;
                    
                    // $product->company_id = $product_info['product_id'];
                    $product->category_id = $category->id;
                    $product->name = $product_info['product_name'];
                    $product->description = $description;
                    // $product->slug = $product_info[''];
                    // $product->description = $product_info[''];
                    $product->price = $product_info['price'];
                    // $product->discount = $product_info[''];
                    $product->stock = $product_info['unit'];
                    $product->min_qty = $product_info['qty'];

                    $product->image = $product_info['product_img'];

                    $product->company_name = $product_info['company_name'];
                    $product->company_url = $product_info['company_url'];

                    $product->save();                

                }

            }

        }

    }

    File::put(storage_path('category2_17.txt'), $id+1);

    return 'success ' . File::get(storage_path('category2_17.txt'));

});
