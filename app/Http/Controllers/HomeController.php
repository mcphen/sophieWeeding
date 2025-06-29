<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Actualite;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Product;
use App\Services\ContactService;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * The contact service instance.
     *
     * @var \App\Services\ContactService
     */
    protected $contactService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ContactService $contactService
     * @return void
     */
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }
    /**
     * Display the homepage
     *
     * @return \Inertia\Response
     */
    public function index(){
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
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }


    /**
     * Display the about page
     *
     * @return \Inertia\Response
     */
    public function about(){
        // Select only the necessary columns for better performance
        $about = About::select(['id', 'content', 'image_path', 'created_at', 'updated_at'])->first();

        // Add image_url if image_path exists
        if ($about && $about->image_path) {
            $about->image_url = StorageHelper::url($about->image_path);
        }

        return Inertia::render('Front/About',[
            'about' => $about,
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display the services page
     *
     * @return \Inertia\Response
     */
    public function services(){
        return Inertia::render('Front/ServiceFront', [
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display the products page with filtering and pagination
     *
     * @param Request $request
     * @return \Inertia\Response
     */
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
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display the portfolio page with filtering and pagination
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function portfolio(Request $request)
    {
        $query = Album::query()
            ->with(['photos' => function($query) {
                $query->select(['id', 'album_id', 'image_path', 'caption']);
            }])
            ->select(['id', 'title', 'event_date', 'theme', 'created_at'])
            ->orderBy('event_date', 'desc');

        // Apply filters
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
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }


    /**
     * Display the blog page with filtering and pagination
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function blog(Request $request)
    {
        $query = Actualite::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date filter
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
                // Specific year
                $query->whereYear('published_at', $date);
            }
        }

        // Sorting
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
            // Default: newest first
            $query->orderBy('published_at', 'desc');
        }

        // Pagination
        $actualites = $query->paginate(9)->withQueryString();

        return Inertia::render('Front/Blog', [
            'actualites' => $actualites,
            'filters' => $request->only(['search', 'date', 'sort']),
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display a single blog article
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function blogShow($id)
    {
        // Select only necessary columns for better performance
        $actualite = Actualite::query()->findOrFail($id);

        // Get related articles (for example, the 3 most recent ones)
        // Optimize by selecting only needed columns
        $relatedActualites = Actualite::query()
            ->select(['id', 'title', 'description', 'image_path', 'published_at', 'slug'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return Inertia::render('Front/BlogArticle', [
            'actualite' => $actualite,
            'relatedArticles' => $relatedActualites,
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display the availability schedule page
     */
    public function availability()
    {
        return Inertia::render('Front/Availability', [
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }

    /**
     * Display a single product
     *
     * @param string $slug
     * @return \Inertia\Response
     */
    public function productShow($slug)
    {
        // Select only necessary columns for better performance
        $product = Product::where('slug', $slug)->firstOrFail();

        // Get related products (for example, the 4 most recent other products)
        // Optimize by selecting only needed columns and limiting the query
        $relatedProducts = Product::where('id', '!=', $product->id)
            ->select(['id', 'title', 'description', 'image_path', 'price', 'slug'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? StorageHelper::url($product->image_path) : null,
                    'price' => $product->price,
                    'slug' => $product->slug,
                ];
            });

        return Inertia::render('Front/ProductDetailFront', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'contactSettings' => $this->contactService->getContactSettings()
        ]);
    }
}
