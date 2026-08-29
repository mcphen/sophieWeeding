<?php

namespace Database\Seeders;

use App\Models\Masterclass;
use App\Models\TrainingSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Journée de sensibilisation à la santé maternelle',
                'niveau' => 'Sensibilisation',
                'description' => "Une matinée d'information et d'échanges autour de la santé maternelle et infantile, ouverte à toutes les familles du quartier.",
                'programme' => "9h00 : Accueil\n9h30 : Atelier suivi de grossesse\n11h00 : Distribution de kits de maternité\n12h00 : Échanges avec l'équipe médicale partenaire",
                'start' => now()->addDays(18)->setTime(9, 0),
                'address' => 'Centre communautaire de Ngor, Dakar',
                'price' => 0,
                'max_participants' => 80,
            ],
            [
                'title' => 'Collecte de fournitures scolaires',
                'niveau' => 'Collecte de fonds',
                'description' => "Venez déposer cahiers, stylos et manuels scolaires pour la rentrée des enfants que nous accompagnons, ou faites un don sur place.",
                'programme' => "Toute la journée : point de collecte ouvert au public\n15h00 : Bilan de la collecte et remerciements",
                'start' => now()->addDays(32)->setTime(9, 0),
                'address' => 'Siège d\'Amaël Fondation, Dakar',
                'price' => 0,
                'max_participants' => null,
            ],
            [
                'title' => 'Journée bénévole : distribution alimentaire',
                'niveau' => 'Bénévolat',
                'description' => "Rejoignez nos équipes pour une journée de distribution de denrées essentielles auprès des familles vulnérables de Dakar.",
                'programme' => "8h00 : Accueil des bénévoles et briefing\n9h00 : Préparation des colis\n10h00 : Distribution dans les quartiers\n13h00 : Débriefing",
                'start' => now()->addDays(45)->setTime(8, 0),
                'address' => 'Point de rassemblement : siège d\'Amaël Fondation, Dakar',
                'price' => 0,
                'max_participants' => 30,
            ],
        ];

        foreach ($events as $event) {
            $masterclass = Masterclass::create([
                'title' => $event['title'],
                'slug' => Str::slug($event['title']),
                'niveau' => $event['niveau'],
                'description' => $event['description'],
                'programme' => $event['programme'],
                'is_active' => true,
            ]);

            TrainingSession::create([
                'masterclass_id' => $masterclass->id,
                'start_date' => $event['start'],
                'end_date' => $event['start']->copy()->addHours(4),
                'location_type' => 'presentiel',
                'adresse' => $event['address'],
                'price' => $event['price'],
                'max_participants' => $event['max_participants'],
                'is_active' => true,
            ]);
        }
    }
}
