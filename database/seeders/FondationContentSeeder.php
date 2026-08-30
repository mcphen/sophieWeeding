<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Contenu institutionnel Amaël Fondation (dossier de présentation).
 *
 * Idempotent : peut être relancé sans créer de doublons.
 *   php artisan db:seed --class=FondationContentSeeder
 */
class FondationContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAbout();
        $this->seedProgrammes();
        $this->seedSettings();
        $this->clearCaches();
    }

    private function seedAbout(): void
    {
        $content = <<<'HTML'
<p class="text-gray-600 leading-relaxed mb-4">
    Amaël Fondation est une organisation à but non lucratif dédiée à l'amélioration des
    conditions de vie des enfants défavorisés. Notre slogan, <strong>« Grandir avec espoir »</strong>,
    résume notre engagement&nbsp;: offrir à chaque enfant une chance réelle de construire son avenir.
    Nos actions se déploient en priorité en Afrique de l'Ouest.
</p>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Notre vision</h3>
<p class="text-gray-600 leading-relaxed mb-4">
    Un monde où chaque enfant peut grandir dans la dignité, avec des opportunités égales.
</p>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Notre mission</h3>
<p class="text-gray-600 leading-relaxed mb-4">
    Apporter un soutien concret aux enfants défavorisés à travers des actions éducatives,
    sociales et solidaires.
</p>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">La problématique</h3>
<p class="text-gray-600 leading-relaxed mb-4">
    Accès limité à l'éducation, précarité, manque de ressources et d'encadrement&nbsp;:
    de nombreux enfants grandissent sans le soutien nécessaire à leur épanouissement.
</p>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Nos objectifs</h3>
<ul class="list-disc pl-5 text-gray-600 space-y-1 mb-4">
    <li>Faciliter l'accès à l'éducation</li>
    <li>Réduire la précarité alimentaire</li>
    <li>Favoriser l'épanouissement personnel</li>
    <li>Encourager l'égalité des chances</li>
</ul>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Nos valeurs</h3>
<ul class="list-disc pl-5 text-gray-600 space-y-1 mb-4">
    <li><strong>Amour</strong></li>
    <li><strong>Solidarité</strong></li>
    <li><strong>Respect</strong></li>
    <li><strong>Espoir</strong></li>
    <li><strong>Engagement</strong></li>
</ul>

<h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Notre stratégie</h3>
<p class="text-gray-600 leading-relaxed mb-4">
    Actions de terrain, partenariats, bénévolat et événements solidaires. Le financement
    repose sur les dons, les partenariats, le sponsoring et les événements.
</p>

<p class="text-gray-600 leading-relaxed mb-4">
    Amaël Fondation agit pour offrir aux enfants une chance réelle de construire leur avenir.
</p>
HTML;

        $about = About::query()->first() ?? new About();
        $about->content = $content;
        $about->save();
    }

    private function seedProgrammes(): void
    {
        $programmes = [
            [
                'title'       => 'Programme Éducation',
                'description' => "Distribution de kits scolaires, prise en charge de la scolarité primaire et soutien scolaire pour permettre à chaque enfant d'apprendre dans de bonnes conditions.",
                'stat_value'  => '10+',
                'stat_label'  => 'enfants scolarisés pris en charge',
            ],
            [
                'title'       => 'Programme Solidarité',
                'description' => "Achat et distribution de vivres, de vêtements et de trousseaux de naissance auprès des orphelinats et des maternités.",
                'stat_value'  => '15',
                'stat_label'  => 'orphelinats visés',
            ],
            [
                'title'       => 'Programme Bien-être',
                'description' => "Activités créatives et récréatives pour favoriser l'épanouissement personnel et redonner confiance aux enfants.",
                'stat_value'  => null,
                'stat_label'  => null,
            ],
            [
                'title'       => 'Événements',
                'description' => "Actions ponctuelles autour de temps forts comme Noël et la rentrée scolaire&nbsp;: distributions, visites et moments de partage.",
                'stat_value'  => '20',
                'stat_label'  => 'maternités visées',
            ],
        ];

        foreach ($programmes as $programme) {
            Service::updateOrCreate(
                ['title' => $programme['title']],
                [
                    'description' => $programme['description'],
                    'stat_value'  => $programme['stat_value'],
                    'stat_label'  => $programme['stat_label'],
                ]
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            'contact_email'    => ['value' => 'amaelfondation@gmail.com',                     'group' => 'contact', 'type' => 'email', 'label' => 'Email de contact'],
            // À vérifier : le dossier ne donne que le nom de page « Amaël Fondation ».
            'social_facebook'  => ['value' => 'https://www.facebook.com/amaelfondation',      'group' => 'social',  'type' => 'url',   'label' => 'Page Facebook'],
            'social_instagram' => ['value' => 'https://www.instagram.com/amael_fondation23',  'group' => 'social',  'type' => 'url',   'label' => 'Compte Instagram'],
        ];

        foreach ($settings as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'group' => $data['group'],
                    'type'  => $data['type'],
                    'label' => $data['label'],
                ]
            );
        }
    }

    private function clearCaches(): void
    {
        Cache::forget('contact_settings');
        Cache::forget('settings_all');

        foreach (['contact_email', 'social_facebook', 'social_instagram'] as $key) {
            Cache::forget('setting_' . $key);
        }

        foreach (['general', 'contact', 'social'] as $group) {
            Cache::forget('settings_group_' . $group);
        }
    }
}
