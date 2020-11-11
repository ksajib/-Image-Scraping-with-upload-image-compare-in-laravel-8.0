<?php
namespace App\Libraries\KubAT\PhpSimple\HtmlDomParser;
namespace App\Http\Controllers;

use App\Models\CompareFile;
use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class FortendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $compare = CompareFile::orderBy('compare_files.id', 'DESC');
        $input = $request->all();
        if (!empty($input['q'])) {
            $compare->Where('compare_files.hash_tag', 'LIKE', '%'.$input['q'].'%');
        }

        $lists = 1;
        $perPage = 25;
        $records = $compare->paginate($perPage);
        $serial = (!empty($input['page']))?(($perPage*($input['page']-1))+1):1;
        return view('list', compact('lists', 'serial', 'records', 'compare'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('landing');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'web_url' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hashTag' => 'required',
        ]);

        $arr = [];
        $matchImageArr = [];
        $input = $request->all();
        $url = $input['web_url'];

        $httpOptions = array('http' => array(
            'method'  => 'GET',
            'ignore_errors' => true
        ));
        $context  = stream_context_create($httpOptions);
        $html = HtmlDomParser::str_get_html(file_get_contents($url, true, $context));

        // Find all images
        if ($html !="") :
            if ($request->hasFile('image') != '') :
                $uploadImage = time().rand().'.'.$request->image->extension();
                $request->image->move(public_path('images'), $uploadImage);
                $arr['upload_image'] = $uploadImage;
            endif;

            $count = 1;
            foreach($html->find('img') as $key => $element) :
                $data = file_get_contents($element->src);
                $urlImage = time().rand().'.jpg';
                file_put_contents(public_path('images').'/'.$urlImage, $data);
                $arr['url_image'][$count] = $urlImage;
                $count++;
            endforeach;

            if ($arr !="") :
                $match_count = 0;
                foreach ($arr['url_image'] as $key => $image) :
                    $compareResult = $this->compare(public_path('images').'/'.$arr['upload_image'],public_path('images').'/'.$image);

                    if ($compareResult <= 10 && is_numeric($compareResult)) :
                        $matchImageArr[$match_count]['image'] = $image;
                        $matchImageArr[$match_count]['match_percent'] = $compareResult;
                        $match_count++;
                    else :
                        $image_path = public_path('images').'/'.$image;
                        if(File::exists($image_path)) :
                            File::delete($image_path);
                        endif;
                    endif;
                endforeach;
            endif;
        endif;

        $status = false;
        if ($matchImageArr !="") :
            foreach ($matchImageArr as $val) :
                $insertData = [
                    'web_url' => $input['web_url'],
                    'image' => $uploadImage,
                    'url_image' => $val['image'],
                    'hash_tag' => $input['hashTag'],
                    'is_image_percent' => $val['match_percent'],
                    'status' => '1'
                ];

                $dd = CompareFile::create($insertData)->toSql();
                $status = true;
            endforeach;
        endif;

        if ($status === true) :
            Session::flash('message', ['status' => true, 'data' => $matchImageArr, 'upload_image' => $uploadImage, 'text' => "Image Match successfully"]);
        else:
            Session::flash('message', ['status' => false, 'data' => $matchImageArr, 'upload_image' => $uploadImage, 'text' => "Image Not Match"]);
        endif;

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompareFile  $compareFile
     * @return \Illuminate\Http\Response
     */
    public function show(CompareFile $compareFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompareFile  $compareFile
     * @return \Illuminate\Http\Response
     */
    public function edit(CompareFile $compareFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompareFile  $compareFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompareFile $compareFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompareFile  $compareFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompareFile $compareFile)
    {
        //
    }

    private function mimeType($i)
	{
		/*returns array with mime type and if its jpg or png. Returns false if it isn't jpg or png*/
		$mime = getimagesize($i);
		$return = array($mime[0],$mime[1]);

		switch ($mime['mime'])
		{
			case 'image/jpeg':
				$return[] = 'jpg';
				return $return;
			case 'image/png':
				$return[] = 'png';
				return $return;
			default:
				return false;
		}
	}

	private function createImage($i)
	{
		/*retuns image resource or false if its not jpg or png*/
		$mime = $this->mimeType($i);

		if($mime[2] == 'jpg')
		{
			return imagecreatefromjpeg ($i);
		}
		else if ($mime[2] == 'png')
		{
			return imagecreatefrompng ($i);
		}
		else
		{
			return false;
		}
	}

	private function resizeImage($i,$source)
	{
		/*resizes the image to a 8x8 squere and returns as image resource*/
		$mime = $this->mimeType($source);
		$t = imagecreatetruecolor(8, 8);
		$source = $this->createImage($source);
		imagecopyresized($t, $source, 0, 0, 0, 0, 8, 8, $mime[0], $mime[1]);
		return $t;
	}

    	private function colorMeanValue($i)
	{
		/*returns the mean value of the colors and the list of all pixel's colors*/
		$colorList = array();
		$colorSum = 0;
		for($a = 0;$a<8;$a++)
		{

			for($b = 0;$b<8;$b++)
			{
				$rgb = imagecolorat($i, $a, $b);
				$colorList[] = $rgb & 0xFF;
				$colorSum += $rgb & 0xFF;
			}
		}

		return array($colorSum/64,$colorList);
	}

    private function bits($colorMean)
	{
		/*returns an array with 1 and zeros. If a color is bigger than the mean value of colors it is 1*/
		$bits = array();
		foreach($colorMean[1] as $color){$bits[]= ($color>=$colorMean[0])?1:0;}
		return $bits;

    }

    public function compare($a,$b)
	{
		/*main function. returns the hammering distance of two images' bit value*/
		$i1 = $this->createImage($a);
		$i2 = $this->createImage($b);

		if(!$i1 || !$i2){return false;}

		$i1 = $this->resizeImage($i1,$a);
		$i2 = $this->resizeImage($i2,$b);

		imagefilter($i1, IMG_FILTER_GRAYSCALE);
		imagefilter($i2, IMG_FILTER_GRAYSCALE);

		$colorMean1 = $this->colorMeanValue($i1);
		$colorMean2 = $this->colorMeanValue($i2);

		$bits1 = $this->bits($colorMean1);
		$bits2 = $this->bits($colorMean2);

		$hammeringDistance = 0;

		for($a = 0;$a<64;$a++)
		{
			if($bits1[$a] != $bits2[$a])
			{
				$hammeringDistance++;
			}
		}

        return $hammeringDistance;
    }
}
