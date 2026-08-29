<?php

namespace Database\Seeders;

use App\Models\Actualite;
use Illuminate\Database\Seeder;

class ActualiteSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Rentrée scolaire 2026 : 500 enfants équipés grâce à vos dons',
                'published_at' => now()->subDays(3),
                'html' => "<p>Cette année encore, Amaël Fondation a distribué des kits scolaires complets (cahiers, manuels, ardoises) à plus de 500 enfants issus de familles vulnérables de Dakar.</p><p>Cette action, rendue possible grâce à la générosité de nos donateurs et à l'engagement de nos bénévoles, permet à chaque enfant de démarrer l'année dans de bonnes conditions. Merci à toutes celles et ceux qui ont contribué à cette collecte.</p>",
            ],
            [
                'title' => 'Un nouveau centre de santé maternelle ouvre ses portes',
                'published_at' => now()->subDays(12),
                'html' => "<p>En partenariat avec des professionnels de santé locaux, nous avons inauguré un nouvel espace d'accueil pour les futures mères dans le quartier de Ngor.</p><p>Ce centre propose un suivi de grossesse, des kits de maternité et des séances de sensibilisation à la santé maternelle et infantile, entièrement gratuits pour les familles accompagnées par la fondation.</p>",
            ],
            [
                'title' => 'Distribution alimentaire de fin d\'année : plus de 200 familles soutenues',
                'published_at' => now()->subDays(25),
                'html' => "<p>À l'approche des fêtes, nos équipes se sont mobilisées pour distribuer des denrées essentielles (riz, huile, sucre) à plus de 200 familles en situation de précarité à Dakar.</p><p>Cette distribution a été rendue possible grâce à la mobilisation de nos bénévoles et au soutien de nos partenaires locaux.</p>",
            ],
            [
                'title' => 'Portrait : Awa, bénévole depuis 3 ans auprès des familles',
                'published_at' => now()->subDays(40),
                'html' => "<p>Awa a rejoint Amaël Fondation il y a trois ans en tant que bénévole. Aujourd'hui, elle coordonne le suivi de plusieurs familles accompagnées par la fondation.</p><p>« Ce qui me touche le plus, c'est de voir les enfants reprendre confiance en eux et retourner à l'école avec le sourire », confie-t-elle.</p>",
            ],
            [
                'title' => 'Amaël Fondation signe un partenariat avec deux écoles de Dakar',
                'published_at' => now()->subDays(55),
                'html' => "<p>Nous sommes heureux d'annoncer un nouveau partenariat avec deux établissements scolaires de Dakar, qui permettra un suivi renforcé des enfants soutenus par la fondation tout au long de l'année.</p><p>Ce partenariat prévoit notamment un accompagnement pédagogique et un dispositif d'alerte précoce en cas de décrochage scolaire.</p>",
            ],
            [
                'title' => 'Bilan 2025 : retour sur une année d\'actions solidaires',
                'published_at' => now()->subDays(70),
                'html' => "<p>L'année 2025 a été marquée par de nombreuses actions de terrain : fournitures scolaires, aide à la maternité, distributions alimentaires et actions d'urgence auprès des familles les plus vulnérables de Dakar.</p><p>Merci à tous nos donateurs, bénévoles et partenaires pour leur confiance et leur engagement à nos côtés.</p>",
            ],
        ];

        foreach ($articles as $article) {
            $actualite = Actualite::create([
                'title' => $article['title'],
                'published_at' => $article['published_at'],
            ]);

            $actualite->blocks()->create([
                'type' => 'text',
                'content' => ['html' => $article['html']],
                'position' => 1,
            ]);
        }
    }
}
