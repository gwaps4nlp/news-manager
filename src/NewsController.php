<?php

namespace Gwaps4nlp\NewsManager;

use Auth,App;
use Illuminate\Http\Request;
use Gwaps4nlp\NewsManager\Requests\NewsRequest;
use Gwaps4nlp\NewsManager\Models\News;
use Gwaps4nlp\Models\User;
use Gwaps4nlp\NewsManager\Requests\NewsCreateRequest;
use App\Http\Controllers\Controller;
use Gwaps4nlp\Repositories\LanguageRepository;
use Gwaps4nlp\NewsManager\Repositories\NewsRepository;


class NewsController extends Controller
{
    /**
     * Create a new newsController instance.
     *
     * @param  App\Repositories\NewsRepository $news       
     * @param  App\Repositories\LanguageRepository $language       
     * @return void
     */
    public function __construct(NewsRepository $news, LanguageRepository $language)
    {
        $this->news = $news;
        $this->language = $language;
    }
    
    /**
     * Show a listing of news.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    { 
        $news = $this->news->getAll();
        $languages = $this->language->getList();
        return view('news-manager::index',compact('news','languages'));
    }

    /**
     * Show a news object.
     *
     * @param  int $id the identifier of the news   
     * @return Illuminate\Http\Response
     */
    public function getShow(News $news)
    {
        return view('news-manager::show',compact('news'));
    }

    /**
     * Show the form to create a new news.
     *
     * @return Illuminate\Http\Response
     */
    public function getCreate()
    {
        $languages = $this->language->getList();        
        return view('news-manager::create',compact('languages'));
    }
    
    /**
     * Create a news
     *
     * @param  App\Http\Requests\NewsCreateRequest $request   
     * @return Illuminate\Http\Response
     */
    public function postCreate(NewsCreateRequest $request)
    {
        if($request->input('send_by_email')){
            $date = $request->input('date_scheduled').' '.$request->input('hour_scheduled');
            $scheduled_at = date_create_from_format ( "d/m/Y H:i", $date );
            $news = $this->news->create(array_merge($request->except('_token'),array('scheduled_at'=>$scheduled_at->format("Y-m-d H:i:s"))));         
        } else
            $news = $this->news->create($request->except('_token'));

        foreach(User::get() as $user)
            $news->users()->save($user);
        return $this->getIndex();
    }

    /**
     * Show the form to edit a news.
     *
     * @param  int  $id     
     * @return Illuminate\Http\Response
     */
    public function getEdit(News $news)
    {
        $languages = $this->language->getList();
        return view('news-manager::edit',compact('news','languages'));
    }  
    
    /**
     * Save the modification of a news.
     *
     * @param  int  $id         
     * @param  App\Http\Requests\NewsCreateRequest $request         
     * @return Illuminate\Http\Response
     */
    public function postEdit(News $news, NewsCreateRequest $request)
    {
        $news = $this->news->getById($id);
        $news->update($request->except('_token'));
        return $this->getIndex();
    }

    /**
     * Delete a news
     *
     * @param  App\Http\Requests\NewsRequest $request       
     * @return Illuminate\Http\Response
     */
    public function postDelete(NewsRequest $request)
    {
        $this->news->destroy($request->input('id'));
        return $this->getIndex();
    }

}
