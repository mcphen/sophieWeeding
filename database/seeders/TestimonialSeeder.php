<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'author_name' => 'Fatou D.',
                'author_title' => 'Mère accompagnée par la fondation',
                'content' => "Grâce à Amaël Fondation, mon fils a pu retourner à l'école avec tout le matériel nécessaire. Je ne pensais pas que quelqu'un pourrait nous aider ainsi, sans rien demander en retour.",
            ],
            [
                'author_name' => 'Awa S.',
                'author_title' => 'Bénévole depuis 3 ans',
                'content' => "Ce qui me touche le plus, c'est de voir les enfants reprendre confiance en eux. Chaque distribution, chaque visite auprès des familles me rappelle pourquoi je me suis engagée.",
            ],
            [
                'author_name' => 'Moussa K.',
                'author_title' => 'Donateur régulier',
                'content' => "Je fais un don chaque mois depuis un an. Ce que j'apprécie, c'est la transparence de l'équipe : on sait exactement à quoi sert notre contribution sur le terrain.",
            ],
            [
                'author_name' => 'Aïssatou N.',
                'author_title' => 'Bénéficiaire du programme santé maternelle',
                'content' => "L'équipe m'a suivie pendant toute ma grossesse et m'a offert un kit de maternité. J'ai pu accoucher dans de bien meilleures conditions grâce à leur accompagnement.",
            ],
        ];

        foreach ($testimonials as $position => $testimonial) {
            Testimonial::create([
                'author_name' => $testimonial['author_name'],
                'author_title' => $testimonial['author_title'],
                'position' => (string) $position,
                'content' => $testimonial['content'],
            ]);
        }
    }
}
