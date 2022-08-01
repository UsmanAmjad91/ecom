<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Myear;
use App\Models\Size;
use App\Models\Productattr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Session\Session;
use Yajra\Datatables\Datatables;

class ProductController extends Controller
{
   
    public function index()
    {
        $title="Product";

        $getdata = DB::table('products')
        ->rightJoin('categories', 'products.category', '=', 'categories.cat_id')
        ->leftJoin('brands', 'products.brand', '=', 'brands.brand_id')
        ->leftJoin('colors', 'products.color', '=', 'colors.color_id')
        ->leftJoin('coupons', 'products.coupon', '=', 'coupons.coupon_id')
        ->leftJoin('myears', 'products.model', '=', 'myears.model_id')
        ->leftJoin('sizes', 'products.size', '=', 'sizes.size_id')
        ->get();
    //    dd($data);
        return view('admin.products.products',compact('title','getdata'));
    }
   
    public function  manage_product()
    {
        $title="Add Product";
        $category =DB::table('categories')->get();
        $brand =DB::table('brands')->get();
        $color =DB::table('colors')->get();
        $coupon =DB::table('coupons')->get();
        $myear =DB::table('myears')->get();
        $size =DB::table('sizes')->get();

        return view('admin.products.manage_products',compact('title','category','brand','color','coupon','myear','size'));
    }

    public function insert_product(Request $request)
    {
    //  dd($request->all());  
     $validator = Validator::make($request->all(), [
        'product' => 'required',
        'product_slug' => 'required|unique:products',
        'cat_id' => 'required',
        'brand_id' => 'required',
        'year_id' => 'required',
        'warranty' => 'required',
        'uses' => 'required',
        'keyword' => 'required',
        'short_desc' => 'required',
        'image1' => 'required|mimes:jpeg,png,jpg,gif,svg|max:4096',
        'image2' => 'required|mimes:jpeg,png,jpg,gif,svg|max:4096',
        'image3' => 'required|mimes:jpeg,png,jpg,gif,svg|max:4096',
        'image4' => 'required|mimes:jpeg,png,jpg,gif,svg|max:4096',
        'imageatrr'=> 'required',
        'sku'=>'required',
        'mrp'=>'required',
        'price'=>'required',
        'qty'=>'required',
        'product_status'=>'required',
    ]);
     
    if ($validator->fails()) {
        return json_encode(array('msgpro' => $validator->errors()->all()));
    }
   
    $file=$request->image1;
    $filename1 = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $filename1);
    

    $file=$request->image2;
    $filename2 = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $filename2);


    $file=$request->image3;
    $filename3 = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $filename3);

    $file=$request->image4;
    $filename4 = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $filename4);
    $count=1;
 $count++;
    $insert_pro = new Product;
    $insert_pro->product_name = $request->post('product');
    $insert_pro->coupon = $request->post('coupon_id');
    $insert_pro->category = $request->post('cat_id');
$cls='color_id'.$count;
    if($request->cls){
        $color = $request->cls;
        }else{
            $color = $request->color_id;
        }
        $string = '';
foreach($color as $b=>$c)
{      
        $string .= $c.',';   
}

$solution = substr($string,0,-1);
$insert_pro->color = $solution;
// print_r($solution);
// dd($solution);
$siz='size_id'.$count;
    if($request->siz){
        // $size = $request->siz;
        $insert_pro->size = $request->siz;
        }else{
            $insert_pro->size = $request->size;
        }
   
    $insert_pro->model = $request->post('year_id');
    $insert_pro->brand = $request->post('brand_id');
    $insert_pro->warranty = $request->post('warranty');
    $insert_pro->uses = $request->post('uses');
    $insert_pro->keywords = $request->post('keyword');
    $insert_pro->short_desc = $request->post('short_desc');
    $insert_pro->technical_specification = $request->post('technical_specification');
    $insert_pro->desc = $request->post('desc');
    $insert_pro->product_slug = $request->post('product_slug');
    $insert_pro->product_status = $request->post('product_status');
    $insert_pro->image1 = $filename1;
    $insert_pro->image2 = $filename2;
    $insert_pro->image3 = $filename3;
    $insert_pro->image4 =$filename4;
    $insert_pro->save();
    $pid=$insert_pro->product_id;


if($file=$request->imageatrr1){ 
    $file=$request->imageatrr1;
    $atrrimage = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $atrrimage);
}else if($file=$request->imageatrr2){
 
    $file=$request->imageatrr2;
    $atrrimage = time().'.'.$file->getClientOriginalName(); 
    $file->move(public_path('admin_assets/product_images'), $atrrimage);
}else{
    // dd($file=$request->imageatrr);
    if($file=$request->imageatrr){
        foreach ($file as $key=>$file) {
            $extension = $file->getClientOriginalName();
            $fileName = time().'-' .$request->name.'.'.$extension; 
            $file->move(public_path('admin_assets/product_images'), $fileName); 
        }}
    
}

$skut='sku'.$count;
$mprr='mrp'.$count;
$pric='price'.$count;
$qt='qty'.$count;

$productattr['products_id']= $pid;
$productattr['name'] = $request->post('product');
if($request->skut){
$skuArr = $request->skut;
}else{
    $skuArr = $request->sku;
}
$string = '';
    foreach($skuArr as $b=>$c)
    {      
            $string .= $c.',';   
    }
    
    $solution = substr($string,0,-1);
    $productattr['sku']= $solution;
if($request->mprr){
    $mrp = $request->mprr;
    }else{
        $mrp = $request->mrp;
    }
    $string = '';
    foreach($mrp as $b=>$c)
    {      
            $string .= $c.',';   
    }
    
    $solution = substr($string,0,-1);
    $productattr['mrp']= $solution;

    if($request->pric){
        $price = $request->pric;
        }else{
            $price = $request->price;
        }
        $string = '';
        foreach($price as $b=>$c)
        {      
                $string .= $c.',';   
        }
        
        $solution = substr($string,0,-1);
        $productattr['price']= $solution;
        if($request->qt){
            $qty = $request->qt;
            }else{
                $qty = $request->qty;
            }
            $string = '';
        foreach($qty as $b=>$c)
        {      
                $string .= $c.',';   
        }
        
        $solution = substr($string,0,-1);
        $productattr['qty']= $solution;

        $productattr['imageatrr']= $fileName;
DB::table('productattrs')->insert($productattr);


    if ($insert_pro) {
        session()->flash('msgpro', 'Succsessfuly Added Product');
        return json_encode(array('message' => 'Succsessfuly Added Product', 'status' => 200));
    } else {
        return json_encode(array('message' => 'Not Inserted Product', 'status' => 500));
    }
    }


    public function product_list(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('products')
        ->leftJoin('brands', 'products.brand', '=', 'brands.brand_id')
        ->leftJoin('categories', 'products.category', '=', 'categories.cat_id')
        ->leftJoin('colors', 'products.color', '=', 'colors.color_id')
        ->leftJoin('coupons', 'products.coupon', '=', 'coupons.coupon_id')
        ->leftJoin('myears', 'products.model', '=', 'myears.model_id')
        ->leftJoin('sizes', 'products.size', '=', 'sizes.size_id')
        ->orderBy('product_id', 'desc')->get();

            foreach ($data as $row) {
                // dd($data);
            //    return json_encode($data);
               return Datatables::of($data)->addIndexColumn()
                ->addColumn('action',function($row){
                    $actionBtn = '<a href="javascript:void(0)"  data-toggle="modal"  data-target="#Modal_Edit"  class="edit btn btn-success btn-sm  item d-inline-flex product_edit" data-product_id="' . $row->product_id . '" data-product="' . $row->product_name . '" data-coupon_name="' . $row->coupon_title . '"
                    data-cat_name="' . $row->cat_name . '" data-color_name="' . $row->color . '" data-size_name="' . $row->size . '"
                    data-brand_name="' . $row->brand . '"  data-year_name="' . $row->year . '"  data-product_slug="' . $row->product_slug . '"
                     data-short_desc="' . $row->short_desc . '"  data-desc="' . $row->desc . '"  data-keywords="' . $row->keywords . '"
                     data-technical_specification="' . $row->technical_specification . '" data-uses="' . $row->uses . '" data-warranty="' . $row->warranty . '"
                     data-image1="' . $row->image1 . '" data-image2="' . $row->image2 . '" data-image3="' . $row->image3 . '" data-image4="' . $row->image4 . '"  data-product_status="' . $row->product_status . '" >Edit</a>'; 
                    $actionBtn .='<a href="javascript:void(0)"  class="delete btn btn-danger btn-sm mt-2 item d-inline-flex  product_delete" data-toggle="modal" data-target="#Modal_Delete"  data-product_id="' . $row->product_id . '" >Delete</a>';
                    if($row->product_status == 1){
                        $actionBtn .= '<a href="javascript:void(0)"  class="product_status_ac btn btn-success btn-sm mt-2 item d-inline-flex  product_status"  data-product_id="' . $row->product_id . '" >Active</a>';
                    }
                    if($row->product_status == 0){
                 $actionBtn .= ' <a href="javascript:void(0)"  class="product_status_de btn btn-warning btn-sm mt-2 item d-inline-flex  product_status"  data-product_id="' . $row->product_id . '" >DeActive</a>';
                     }
                    
                    return $actionBtn;
                })
                ->rawColumns(['action'])->make(true);
            }
        }
     }

     public function product_status_de(Request $request,$id){

        if (!empty($id)) {
            // dd($id);
            $isst =  DB::table('products')->where('product_id', $id)->update(array('product_status' => '1'));  
            if (!empty($isst)){
                session()->flash('msgpro', 'Succsessfuly Active Product');
                return json_encode(array('message' => 'Product Active successfully', 'status' => 200));
            }else{
                return json_encode(array('message' => 'Product Not Active', 'status' => 500));
            }
            }
     }
     public function product_status_ac(Request $request,$id){
    //  dd($id);
    if (!empty($id)) {
        // dd($id);
        $isstrt =  DB::table('products')->where('product_id', $id)->update(array('product_status' => '0'));
        if (!empty($isstrt)){
            session()->flash('msgpro', 'Succsessfuly Deactive Product');
            return json_encode(array('message' => 'Product Deactive successfully', 'status' => 200));
         } else{
            return json_encode(array('message' => 'Product Not Deactive', 'status' => 500));
         }
        }
    }
    public function destroy_product(Request $request,$id)
    {
        // dd($id);
        if (!empty($id)) {
            $is_delete = Product::where('product_id', $id)->delete();
            session()->flash('msgpro', 'Succsessfuly Delete Product');
            if (!empty($is_delete))
            
                return json_encode(array('message' => 'Record Deleted successfully', 'status' => 200));
            else
                return json_encode(array('message' => 'Record  Not Deleted', 'status' => 500));
        }
    }

}
