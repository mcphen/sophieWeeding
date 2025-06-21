<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Actualite;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Get contact settings for front-end pages
     *
     * @return array
     */
    private function getContactSettings()
    {
        return [
            'contact_phone' => Setting::get('contact_phone', '(+221) 78 538 30 69'),
            'contact_phone_fixed' => Setting::get('contact_phone_fixed', '(+221) 33 865 27 11'),
            'contact_email' => Setting::get('contact_email', 'sophieweddings5@gmail.com'),
            'contact_address' => Setting::get('contact_address', 'Rue NG-70, 91 Ngor Almadies, Dakar 12000'),
            'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/Sophieweddingsdreams22/'),
           // 'social_twitter' => Setting::get('social_twitter', '#'),
            'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/sophie_weddings_dreams/'),
            'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi: 8am - 6pm'),
        ];
    }
    public function index(){
       // echo Hash::make('123456789'); die();
        // Fetch banner photos
        $bannerPhotos = Photo::where('is_banner', true)
            ->orderBy('banner_order')
            ->limit(4)
            ->get(['id', 'image_path', 'caption']);

        // Append image_url attribute to each photo
        $bannerPhotos->each(function($photo) {
            $photo->append('image_url');
        });

        return Inertia::render('Welcome', [
            'bannerPhotos' => $bannerPhotos,
            'contactSettings' => $this->getContactSettings()
        ]);
    }


    public function about(){
        $about = About::first();
        return Inertia::render('Front/About',[
            'about' => $about,
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function services(){
        return Inertia::render('Front/ServiceFront', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function products(Request $request){
        $query = Product::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply price filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply sorting
        if ($request->has('sort') && $request->sort) {
            $sortField = 'created_at';
            $sortDirection = 'desc';

            if ($request->sort === 'price_asc') {
                $sortField = 'price';
                $sortDirection = 'asc';
            } elseif ($request->sort === 'price_desc') {
                $sortField = 'price';
                $sortDirection = 'desc';
            } elseif ($request->sort === 'title_asc') {
                $sortField = 'title';
                $sortDirection = 'asc';
            } elseif ($request->sort === 'title_desc') {
                $sortField = 'title';
                $sortDirection = 'desc';
            } elseif ($request->sort === 'newest') {
                $sortField = 'created_at';
                $sortDirection = 'desc';
            } elseif ($request->sort === 'oldest') {
                $sortField = 'created_at';
                $sortDirection = 'asc';
            }

            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Front/ProductsFront', [
            'products' => $products,
            'filters' => $request->only(['search', 'min_price', 'max_price', 'sort']),
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function portfolio(Request $request)
    {
        $query = Album::query()
            ->with(['photos' => function($query) {
                $query->select(['id', 'album_id', 'image_path', 'caption']);
            }])
            ->select(['id', 'title', 'event_date', 'theme', 'created_at'])
            ->orderBy('event_date', 'desc');

        // Appliquer les filtres
        if ($request->has('theme') && $request->theme) {
            $query->where('theme', $request->theme);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('theme', 'like', "%{$search}%");
            });
        }

        if ($request->has('year') && $request->year) {
            $year = $request->year;
            $query->whereRaw('YEAR(event_date) = ?', [$year]);
        }

        // Pagination
        $albums = $query->paginate(12)->withQueryString();

        // Append image_url attribute to each photo in each album
        $albums->each(function($album) {
            $album->photos->each(function($photo) {
                $photo->append('image_url');
            });
        });

        return Inertia::render('Front/Portfolio', [
            'albums' => $albums,
            'filters' => $request->only(['theme', 'search', 'year']),
            'contactSettings' => $this->getContactSettings()
        ]);
    }


    public function blog(Request $request)
    {
        $query = Actualite::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Filtre de recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par date
        if ($request->has('date') && $request->date) {
            $date = $request->date;

            if ($date === 'this-month') {
                $query->whereMonth('published_at', Carbon::now()->month)
                    ->whereYear('published_at', Carbon::now()->year);
            } elseif ($date === 'last-month') {
                $lastMonth = Carbon::now()->subMonth();
                $query->whereMonth('published_at', $lastMonth->month)
                    ->whereYear('published_at', $lastMonth->year);
            } elseif (is_numeric($date)) {
                // Année spécifique
                $query->whereYear('published_at', $date);
            }
        }

        // Tri
        if ($request->has('sort') && $request->sort) {
            $sort = $request->sort;

            if ($sort === 'oldest') {
                $query->orderBy('published_at', 'asc');
            } elseif ($sort === 'title-asc') {
                $query->orderBy('title', 'asc');
            } elseif ($sort === 'title-desc') {
                $query->orderBy('title', 'desc');
            }
        } else {
            // Par défaut: plus récent d'abord
            $query->orderBy('published_at', 'desc');
        }

        // Pagination
        $actualites = $query->paginate(9)->withQueryString();

        return Inertia::render('Front/Blog', [
            'actualites' => $actualites,
            'filters' => $request->only(['search', 'date', 'sort']),
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function blogShow($id)
    {
        $actualite = Actualite::query()->findOrFail($id);

        // Récupérer les articles connexes (par exemple, les 3 plus récents)
        $relatedActualites = Actualite::query()->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return Inertia::render('Front/BlogArticle', [
            'actualite' => $actualite,
            'relatedArticles' => $relatedActualites,
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Display the availability schedule page
     */
    public function availability()
    {
        return Inertia::render('Front/Availability', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Display a single product
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function productShow($id)
    {
        $product = Product::findOrFail($id);

        // Get related products (for example, the 4 most recent other products)
        $relatedProducts = Product::where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? \App\Helpers\StorageHelper::url($product->image_path) : null,
                    'price' => $product->price,
                ];
            });

        return Inertia::render('Front/ProductDetailFront', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'contactSettings' => $this->getContactSettings()
        ]);
    }
}
