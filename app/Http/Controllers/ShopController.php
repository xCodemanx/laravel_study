<?php

namespace App\Http\Controllers;

use App\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shops = Shop::all();
        return view('shops.index',compact('shops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status_code;
        $text_alert;
        try {
           Shop::create($request->json()->all());
           $status_code=200;
           $text_alert="Success";
        } catch (\Exception $e) {
           $status_code=422;
           $text_alert=$e;
        }
        return response()->json([$text_alert], $status_code);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        /*$shops = DB::select('select *,TO_BASE64(img) img from shops');
        return response()->json($shops);*/
        $data = $request->all();
        $page=$data['page'];
        $page_size=$data['pageSize'];

        $filter = json_decode($data['filtered'], true); // การ get json object ต้องdecode ก่อน
        $sort = json_decode($data['sorted'],true); // การ get json object ต้องdecode ก่อน
        
        $shop_name=$description=$sale="";
        if(count($filter)>0) {
            if(!empty($filter['shop_name'])) {
                $shop_name=$filter['shop_name'];
            }
            if(!empty($filter['sale'])) {
                $sale=$filter['sale'];
            }
            if(!empty($filter['description'])) {
                $description=$filter['description'];
            }
            $page=1;
        }
        //$shop_name = $request->input('shop_name');
        //$sale = $request->input('sale');
        //$description = $request->input('description');
        //$query = $request->all(); // ใช้สำหรับการรวมค่า GET เป็น Array
        //$shops = DB::select('select *,TO_BASE64(img) img from shops');
        /*$shops = Shop::select('id','shop_name')
                ->where('id',1)
                ->where('shop_name','MK1')->get();*/
        //$shops = DB::table('shops')
        $shops = Shop::select(DB::raw('*,TO_BASE64(img) as img'))
                ->where('shop_name','like','%'.$shop_name.'%')
                ->where('sale','like','%'.$sale.'%')
                ->where('description','like','%'.$description.'%')
                ->orderBy($sort['field'],$sort['sortby']);
        $count = $shops->count();
        $shops = $shops->skip(($page_size*($page-1)))
                       ->take($page_size)
                       ->get();
        return response()->json(['total_pages' => ceil($count/$page_size),'current_pages' => $page,'rows'=>$shops]);
        //return $shops;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $status_code;
        $text_alert;
        try {
           $data = $request->json()->all();
           // วิธีที่ 1 Mass update
           Shop::where('id',$data['id'])->update($data);

           // วิธีที่ 2
           /*$flight = App\Flight::find(1);
           $flight->name = 'New Flight Name';
           $flight->save();*/

           $status_code=200;
           $text_alert="Success";
        } catch (\Exception $e) {
           $status_code=422;
           $text_alert=$e;
        }
        return response()->json([$text_alert], $status_code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        //
    }
}
