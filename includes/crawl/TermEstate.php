<?php


namespace Crawling_WP;


class TermEstate
{

    private $terms = [];

    public function save($post_id)
    {
        foreach ($this->terms as $taxonomy => $term) {
            wp_set_post_terms($post_id, self::getTerms($term, $taxonomy), $taxonomy);
        }
    }

    /**
     * @return array
     */
    protected function getTerms($term_data, $taxonomy)
    {
        $terms = [];

        if (! is_array($term_data)) {
            $term_data = [$term_data];
        }

        foreach ($term_data as $value) {
            $term = get_term_by('slug', sanitize_title($value), $taxonomy, ARRAY_A);
            if (! $term) {
                $term = wp_create_term($value, $taxonomy);
            }
            if ($term instanceof \WP_Error) {
                continue;
            }

            $terms[] = $term['term_id'];
        }

        return $terms;
    }

    /**
     * @param $feature
     * @return bool
     */
    public function add($taxonomy, $term)
    {
        if ($term) {
            $this->terms[$taxonomy] = $term;
        }
    }
}