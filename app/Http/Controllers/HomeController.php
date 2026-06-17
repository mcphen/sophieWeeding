<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Actualite;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Masterclass;
use App\Models\TrainingSession;
use App\Services\ContactService;
use App\Services\CtaService;
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
     * The CTA service instance.
     *
     * @var \App\Services\CtaService
     */
    protected $ctaService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ContactService $contactService
     * @param \App\Services\CtaService $ctaService
     * @return void
     */
    public function __construct(ContactService $contactService, CtaService $ctaService)
    {
        $this->contactService = $contactService;
        $this->ctaService = $ctaService;
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

        $upcomingSessions = TrainingSession::with('masterclass')
            ->active()
            ->upcoming()
            ->orderBy('start_date')
            ->limit(4)
            ->get()
            ->map(fn($s) => [
                'id'              => $s->id,
                'start_date'      => $s->start_date->format('d/m/Y'),
                'start_time'      => $s->start_date->format('H:i'),
                'location_label'  => $s->location_label,
                'formatted_price' => $s->formatted_price,
                'available_spots' => $s->available_spots,
                'masterclass' => [
                    'title'     => $s->masterclass->title,
                    'niveau'    => $s->masterclass->niveau,
                    'slug'      => $s->masterclass->slug,
                    'image_url' => $s->masterclass->image_url,
                ],
            ]);

        return Inertia::render('Welcome', [
            'bannerPhotos'    => $bannerPhotos,
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings'     => $this->ctaService->getCtaSettings(),
            'upcomingSessions' => $upcomingSessions,
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
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
        ]);
    }

    /**
     * Display the services page
     *
     * @return \Inertia\Response
     */
    public function services(){
        return Inertia::render('Front/ServiceFront', [
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
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
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
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
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
        ]);
    }


    /**
     * Display the blog page
     *
     * @return \Inertia\Response
     */
    public function blog()
    {
        return Inertia::render('Front/Blog', [
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
        ]);
    }

    /**
     * Display a single blog article
     *
     * @param string $slug
     * @return \Inertia\Response
     */
    public function blogShow($slug)
    {
        // Select only necessary columns for better performance
        $actualite = Actualite::query()->where('slug', $slug)->with('blocks')->firstOrFail();

        // Get related articles (for example, the 3 most recent ones)
        // Optimize by selecting only needed columns
        $relatedActualites = Actualite::query()
            ->select(['id', 'title',  'image_path', 'published_at', 'slug'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $actualite->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return Inertia::render('Front/BlogArticle', [
            'actualite' => $actualite,
            'relatedArticles' => $relatedActualites,
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
        ]);
    }

    /**
     * Display the availability schedule page
     */
    public function availability()
    {
        return Inertia::render('Front/Availability', [
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
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
            ->select(['id', 'title',  'image_path', 'price', 'slug'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                   // 'description' => $product->description,
                    'image_url' => $product->image_path ? StorageHelper::url($product->image_path) : null,
                    'price' => $product->price,
                    'slug' => $product->slug,
                ];
            });

        return Inertia::render('Front/ProductDetailFront', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings' => $this->ctaService->getCtaSettings()
        ]);
    }

    public function masterclasses(Request $request)
    {
        $query = Masterclass::active()->withCount('sessions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('niveau', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $masterclasses = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();

        $masterclasses->through(function ($mc) {
            $mc->append(['image_url', 'upcoming_sessions_count']);
            return $mc;
        });

        return Inertia::render('Front/Masterclasses', [
            'masterclasses'   => $masterclasses,
            'filters'         => $request->only(['search']),
            'contactSettings' => $this->contactService->getContactSettings(),
            'ctaSettings'     => $this->ctaService->getCtaSettings(),
        ]);
    }

    public function masterclassShow(string $slug)
    {
        $masterclass = Masterclass::where('slug', $slug)->active()->firstOrFail();

        $sessions = $masterclass->sessions()
            ->active()
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'start_date'       => $s->start_date->format('d/m/Y'),
                'start_time'       => $s->start_date->format('H:i'),
                'end_time'         => $s->end_date?->format('H:i'),
                'start_date_iso'   => $s->start_date->toIso8601String(),
                'end_date_iso'     => $s->end_date?->toIso8601String(),
                'location_type'    => $s->location_type,
                'location_label'   => $s->location_label,
                'adresse'          => $s->adresse,
                'google_maps_url'  => $s->google_maps_url,
                'online_link'      => $s->online_link,
                'formatted_price'  => $s->formatted_price,
                'price_raw'        => $s->price,
                'max_participants' => $s->max_participants,
                'available_spots'  => $s->available_spots,
                'is_full'          => $s->isFull(),
            ]);

        $otherMasterclasses = Masterclass::active()
            ->where('id', '!=', $masterclass->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(fn($mc) => [
                'id'        => $mc->id,
                'title'     => $mc->title,
                'niveau'    => $mc->niveau,
                'image_url' => $mc->image_url,
                'slug'      => $mc->slug,
            ]);

        $ogImage = $masterclass->image_url ? url($masterclass->image_url) : null;

        return Inertia::render('Front/MasterclassDetail', [
            'masterclass'       => [
                'id'           => $masterclass->id,
                'title'        => $masterclass->title,
                'niveau'       => $masterclass->niveau,
                'description'  => $masterclass->description,
                'programme'    => $masterclass->programme,
                'image_url'    => $masterclass->image_url,
                'document_url' => $masterclass->document_url,
                'slug'         => $masterclass->slug,
            ],
            'sessions'          => $sessions,
            'otherMasterclasses' => $otherMasterclasses,
            'contactSettings'   => $this->contactService->getContactSettings(),
            'ctaSettings'       => $this->ctaService->getCtaSettings(),
            'og'                => [
                'title'       => $masterclass->title . ' – Niveau ' . $masterclass->niveau,
                'description' => $masterclass->description
                    ?? 'Masterclass ' . $masterclass->niveau . ' organisée par Sophie Weddings Dream.',
                'image'       => $ogImage,
                'url'         => request()->url(),
            ],
        ]);
    }
}
