<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearchIndex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_search_indexes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'lang_id',
        'search_index',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'lang_id' => 'integer',
    ];

    /**
     * Get the product that owns the search index.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the language of the search index.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    /**
     * Scope a query to only include search indexes for a specific product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to only include search indexes for a specific language.
     */
    public function scopeForLanguage(Builder $query, int $languageId): Builder
    {
        return $query->where('lang_id', $languageId);
    }

    /**
     * Perform a full-text search on the search_index column.
     *
     * @param Builder $query
     * @param string $searchTerm
     * @param int|null $languageId
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $searchTerm, ?int $languageId = null): Builder
    {
        $query->whereRaw(
            "MATCH(search_index) AGAINST(? IN BOOLEAN MODE)",
            [$this->prepareSearchTerm($searchTerm)]
        );

        if ($languageId !== null) {
            $query->where('lang_id', $languageId);
        }

        return $query;
    }

    /**
     * Update or create a search index for a product.
     *
     * @param int $productId
     * @param int $languageId
     * @param array $terms
     * @return self
     */
    public static function updateIndex(int $productId, int $languageId, array $terms): self
    {
        $searchIndex = implode(' ', array_unique(array_map('trim', $terms)));
        
        return self::updateOrCreate(
            [
                'product_id' => $productId,
                'lang_id' => $languageId,
            ],
            ['search_index' => $searchIndex]
        );
    }

    /**
     * Prepare search term for full-text search.
     *
     * @param string $searchTerm
     * @return string
     */
    protected function prepareSearchTerm(string $searchTerm): string
    {
        // Remove special characters and extra spaces
        $searchTerm = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $searchTerm);
        $searchTerm = trim($searchTerm);
        
        // Split into words and add wildcards
        $words = explode(' ', $searchTerm);
        $words = array_map('trim', $words);
        $words = array_filter($words);
        
        // Add wildcards and + operator for AND logic
        $preparedTerms = array_map(function($word) {
            return '+' . $word . '*';
        }, $words);
        
        return implode(' ', $preparedTerms);
    }

    /**
     * Get search suggestions based on the search term.
     *
     * @param string $searchTerm
     * @param int|null $languageId
     * @param int $limit
     * @return array
     */
    public static function getSuggestions(string $searchTerm, ?int $languageId = null, int $limit = 10): array
    {
        $query = self::query();
        
        if ($languageId !== null) {
            $query->where('lang_id', $languageId);
        }
        
        $results = $query->where('search_index', 'LIKE', '%' . $searchTerm . '%')
                        ->groupBy('search_index')
                        ->limit($limit)
                        ->pluck('search_index')
                        ->toArray();
        
        $suggestions = [];
        foreach ($results as $result) {
            $terms = explode(' ', $result);
            foreach ($terms as $term) {
                if (stripos($term, $searchTerm) === 0 && !in_array($term, $suggestions, true)) {
                    $suggestions[] = $term;
                    if (count($suggestions) >= $limit) {
                        break 2;
                    }
                }
            }
        }
        
        return $suggestions;
    }

    /**
     * Rebuild the search index for a product.
     *
     * @param Product $product
     * @return void
     */
    public static function rebuildForProduct(Product $product): void
    {
        // Get all product details in all languages
        $details = $product->details()->with('language')->get();
        
        foreach ($details as $detail) {
            $terms = [
                $detail->title,
                $detail->short_description,
                $product->sku,
                $product->id,
            ];
            
            // Add category names if needed
            if ($product->category) {
                $terms[] = $product->category->name;
                if ($product->category->parent) {
                    $terms[] = $product->category->parent->name;
                }
            }
            
            // Filter out empty terms and trim
            $terms = array_filter(array_map('trim', $terms));
            
            // Update the search index
            self::updateIndex($product->id, $detail->lang_id, $terms);
        }
    }
}
