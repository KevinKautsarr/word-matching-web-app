<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\Lesson;
use App\Models\Vocabulary;
use App\Models\UserProgress;
use App\Models\UserXpLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. DISABLE FK & TRUNCATE ─────────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        UserXpLog::truncate();
        UserProgress::truncate();
        Vocabulary::truncate();
        Lesson::truncate();
        Unit::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── 2. USER DEMO ─────────────────────────────────────────────────
        $user = User::create([
            'name'           => 'Ahmad Rizki',
            'email'          => 'demo@lexora.id',
            'password'       => Hash::make('password'),
            'xp'             => 150,
            'level'          => 1,
            'streak'         => 3,
            'last_active_at' => now(),
        ]);

        // ── 3. UNITS + LESSONS + VOCABULARIES ────────────────────────────

        // ══════════════════════════════════════════════════════════════════
        // UNIT 1 — Daily Vocabulary
        // ══════════════════════════════════════════════════════════════════
        $unit1 = Unit::create([
            'title'       => 'Daily Vocabulary',
            'description' => 'Kosakata sehari-hari yang sering digunakan',
            'icon'        => '🏠',
            'order'       => 1,
            'is_locked'   => false,
        ]);

        // Lesson 1 — Greetings
        $greetingsLesson = Lesson::create([
            'unit_id'     => $unit1->id,
            'title'       => 'Greetings',
            'description' => 'Belajar salam dan sapaan dasar dalam bahasa Inggris',
            'order'       => 1,
            'is_locked'   => false,
            'xp_reward'   => 50,
        ]);
        $this->seedVocabularies($greetingsLesson->id, [
            ['Hello',         'Halo',             'Hello, how are you?'],
            ['Goodbye',       'Selamat tinggal',  'Goodbye, see you tomorrow!'],
            ['Please',        'Tolong',           'Please help me.'],
            ['Thank you',     'Terima kasih',     'Thank you very much.'],
            ['Sorry',         'Maaf',             'Sorry for being late.'],
            ['Yes',           'Ya',               'Yes, I agree with you.'],
            ['No',            'Tidak',            'No, I don\'t want that.'],
            ['Good morning',  'Selamat pagi',     'Good morning everyone!'],
            ['Good night',    'Selamat malam',    'Good night, sleep well.'],
            ['Welcome',       'Selamat datang',   'Welcome to our school.'],
        ]);

        // Lesson 2 — Numbers & Colors
        $colorsLesson = Lesson::create([
            'unit_id'     => $unit1->id,
            'title'       => 'Numbers & Colors',
            'description' => 'Angka dan warna dalam bahasa Inggris',
            'order'       => 2,
            'is_locked'   => true,
            'xp_reward'   => 50,
        ]);
        $this->seedVocabularies($colorsLesson->id, [
            ['One',    'Satu',   'I have one book.'],
            ['Two',    'Dua',    'She has two cats.'],
            ['Three',  'Tiga',   'There are three apples.'],
            ['Red',    'Merah',  'The apple is red.'],
            ['Blue',   'Biru',   'The sky is blue.'],
            ['Green',  'Hijau',  'The grass is green.'],
            ['Yellow', 'Kuning', 'The sun looks yellow.'],
            ['Black',  'Hitam',  'The night is black.'],
            ['White',  'Putih',  'Snow is white.'],
            ['Purple', 'Ungu',   'She loves purple flowers.'],
        ]);

        // Lesson 3 — Food & Drinks
        $foodLesson = Lesson::create([
            'unit_id'     => $unit1->id,
            'title'       => 'Food & Drinks',
            'description' => 'Kosakata makanan dan minuman',
            'order'       => 3,
            'is_locked'   => true,
            'xp_reward'   => 50,
        ]);
        $this->seedVocabularies($foodLesson->id, [
            ['Rice',      'Nasi',    'I eat rice every day.'],
            ['Water',     'Air',     'Drink more water daily.'],
            ['Bread',     'Roti',    'I eat bread for breakfast.'],
            ['Milk',      'Susu',    'Children need to drink milk.'],
            ['Egg',       'Telur',   'She boiled an egg.'],
            ['Chicken',   'Ayam',    'We had chicken for dinner.'],
            ['Fish',      'Ikan',    'Fish is healthy food.'],
            ['Fruit',     'Buah',    'Eat more fruit every day.'],
            ['Vegetable', 'Sayuran', 'Vegetables are very healthy.'],
            ['Coffee',    'Kopi',    'He drinks coffee every morning.'],
        ]);

        // Lesson 4 — Body Parts
        $bodyLesson = Lesson::create([
            'unit_id'     => $unit1->id,
            'title'       => 'Body Parts',
            'description' => 'Bagian-bagian tubuh dalam bahasa Inggris',
            'order'       => 4,
            'is_locked'   => true,
            'xp_reward'   => 50,
        ]);
        $this->seedVocabularies($bodyLesson->id, [
            ['Head',   'Kepala',  'My head hurts today.'],
            ['Eye',    'Mata',    'Her eyes are brown.'],
            ['Ear',    'Telinga', 'He has big ears.'],
            ['Nose',   'Hidung',  'The rose smells good.'],
            ['Mouth',  'Mulut',   'Open your mouth wide.'],
            ['Hand',   'Tangan',  'Wash your hands often.'],
            ['Foot',   'Kaki',    'My foot hurts after running.'],
            ['Hair',   'Rambut',  'Her hair is very long.'],
            ['Heart',  'Jantung', 'Exercise is good for your heart.'],
            ['Back',   'Punggung','My back aches today.'],
        ]);

        // Lesson 5 — Animals
        $animalsLesson = Lesson::create([
            'unit_id'     => $unit1->id,
            'title'       => 'Animals',
            'description' => 'Nama-nama hewan dalam bahasa Inggris',
            'order'       => 5,
            'is_locked'   => true,
            'xp_reward'   => 50,
        ]);
        $this->seedVocabularies($animalsLesson->id, [
            ['Cat',       'Kucing',     'The cat is sleeping.'],
            ['Dog',       'Anjing',     'The dog is barking loudly.'],
            ['Bird',      'Burung',     'The bird is singing.'],
            ['Fish',      'Ikan',       'The fish swims very fast.'],
            ['Tiger',     'Harimau',    'The tiger is dangerous.'],
            ['Elephant',  'Gajah',      'Elephants are very large.'],
            ['Rabbit',    'Kelinci',    'The rabbit is very fluffy.'],
            ['Snake',     'Ular',       'Be careful of the snake.'],
            ['Monkey',    'Monyet',     'Monkeys love bananas.'],
            ['Butterfly', 'Kupu-kupu', 'The butterfly is beautiful.'],
        ]);

        // ══════════════════════════════════════════════════════════════════
        // UNIT 2 — Emotions & Feelings
        // ══════════════════════════════════════════════════════════════════
        $unit2 = Unit::create([
            'title'       => 'Emotions & Feelings',
            'description' => 'Kata-kata yang menggambarkan perasaan',
            'icon'        => '😊',
            'order'       => 2,
            'is_locked'   => true,
        ]);

        $lessonsUnit2 = [
            [
                'title' => 'Basic Emotions', 'order' => 1, 'locked' => false,
                'desc'  => 'Emosi dasar yang sering dirasakan',
                'vocab' => [
                    ['Happy',    'Senang',   'I am happy today.'],
                    ['Sad',      'Sedih',    'She felt sad after the news.'],
                    ['Angry',    'Marah',    'He was angry at the mistake.'],
                    ['Scared',   'Takut',    'The child is scared of the dark.'],
                    ['Surprised','Terkejut', 'She was surprised by the gift.'],
                    ['Bored',    'Bosan',    'He is bored with the lesson.'],
                    ['Excited',  'Antusias', 'They are excited about the trip.'],
                    ['Tired',    'Lelah',    'She is tired after work.'],
                    ['Confused', 'Bingung',  'He looks confused.'],
                    ['Calm',     'Tenang',   'Take a deep breath and stay calm.'],
                ],
            ],
            [
                'title' => 'Positive Feelings', 'order' => 2, 'locked' => true,
                'desc'  => 'Perasaan positif dan menyenangkan',
                'vocab' => [
                    ['Joyful',     'Gembira',   'She felt joyful at the party.'],
                    ['Grateful',   'Bersyukur', 'I am grateful for everything.'],
                    ['Proud',      'Bangga',    'He is proud of his achievement.'],
                    ['Hopeful',    'Penuh harapan','She remained hopeful.'],
                    ['Confident',  'Percaya diri','He is confident in himself.'],
                    ['Peaceful',   'Damai',     'The garden feels peaceful.'],
                    ['Cheerful',   'Ceria',     'She is always cheerful.'],
                    ['Optimistic', 'Optimis',   'Stay optimistic about the future.'],
                    ['Inspired',   'Terinspirasi','He felt inspired by the speech.'],
                    ['Relieved',   'Lega',      'She felt relieved after the exam.'],
                ],
            ],
            [
                'title' => 'Negative Feelings', 'order' => 3, 'locked' => true,
                'desc'  => 'Perasaan negatif yang perlu dikenali',
                'vocab' => [
                    ['Anxious',    'Cemas',      'She feels anxious before exams.'],
                    ['Jealous',    'Cemburu',    'He is jealous of his friend.'],
                    ['Frustrated', 'Frustrasi',  'She is frustrated with the problem.'],
                    ['Lonely',     'Kesepian',   'He feels lonely at home.'],
                    ['Guilty',     'Bersalah',   'She feels guilty about lying.'],
                    ['Ashamed',    'Malu',       'He was ashamed of his behavior.'],
                    ['Disappointed','Kecewa',    'She was disappointed with the result.'],
                    ['Nervous',    'Gugup',      'He is nervous before the speech.'],
                    ['Hopeless',   'Putus asa',  'She felt hopeless at that moment.'],
                    ['Envious',    'Iri hati',   'He is envious of others\' success.'],
                ],
            ],
            [
                'title' => 'Mood Words', 'order' => 4, 'locked' => true,
                'desc'  => 'Kata-kata untuk menggambarkan suasana hati',
                'vocab' => [
                    ['Moody',      'Moody',       'He is very moody today.'],
                    ['Cranky',     'Rewel',        'The baby is cranky this morning.'],
                    ['Melancholy', 'Melankolis',   'She has a melancholy look.'],
                    ['Gloomy',     'Murung',       'He looks gloomy all day.'],
                    ['Ecstatic',   'Sangat gembira','She was ecstatic about the news.'],
                    ['Content',    'Puas',         'He is content with his life.'],
                    ['Indifferent','Acuh tak acuh','She seemed indifferent to it.'],
                    ['Nostalgic',  'Nostalgia',    'The song made her feel nostalgic.'],
                    ['Restless',   'Gelisah',      'He is restless and cannot sleep.'],
                    ['Sentimental','Sentimental',  'She gets sentimental easily.'],
                ],
            ],
            [
                'title' => 'Expressions', 'order' => 5, 'locked' => true,
                'desc'  => 'Ekspresi perasaan dalam kalimat',
                'vocab' => [
                    ['Cry',     'Menangis',  'She started to cry.'],
                    ['Laugh',   'Tertawa',   'He laughed at the joke.'],
                    ['Smile',   'Tersenyum', 'She smiled warmly.'],
                    ['Sigh',    'Menghela napas','He sighed with relief.'],
                    ['Shout',   'Berteriak', 'Don\'t shout in the library.'],
                    ['Whisper', 'Berbisik',  'She whispered a secret.'],
                    ['Hug',     'Memeluk',   'She hugged her mother tightly.'],
                    ['Cheer',   'Bersorak',  'The crowd cheered loudly.'],
                    ['Worry',   'Khawatir',  'Don\'t worry, it will be fine.'],
                    ['Comfort', 'Menghibur', 'He comforted his sad friend.'],
                ],
            ],
        ];

        foreach ($lessonsUnit2 as $data) {
            $lesson = Lesson::create([
                'unit_id'     => $unit2->id,
                'title'       => $data['title'],
                'description' => $data['desc'],
                'order'       => $data['order'],
                'is_locked'   => $data['locked'],
                'xp_reward'   => 50,
            ]);
            $this->seedVocabularies($lesson->id, $data['vocab']);
        }

        // ══════════════════════════════════════════════════════════════════
        // UNIT 3 — Nature & Environment
        // ══════════════════════════════════════════════════════════════════
        $unit3 = Unit::create([
            'title'       => 'Nature & Environment',
            'description' => 'Kosakata alam dan lingkungan sekitar',
            'icon'        => '🌿',
            'order'       => 3,
            'is_locked'   => true,
        ]);

        $lessonsUnit3 = [
            [
                'title' => 'Weather', 'order' => 1, 'locked' => false,
                'desc'  => 'Cuaca dan kondisi alam',
                'vocab' => [
                    ['Rain',      'Hujan',    'It is raining outside.'],
                    ['Sun',       'Matahari', 'The sun is shining brightly.'],
                    ['Wind',      'Angin',    'The wind is blowing hard.'],
                    ['Cloud',     'Awan',     'Dark clouds filled the sky.'],
                    ['Snow',      'Salju',    'Snow covered the mountains.'],
                    ['Thunder',   'Petir',    'Thunder roared in the storm.'],
                    ['Storm',     'Badai',    'A big storm is coming.'],
                    ['Fog',       'Kabut',    'Fog covered the valley.'],
                    ['Rainbow',   'Pelangi',  'A rainbow appeared after rain.'],
                    ['Lightning', 'Kilat',    'Lightning struck the tree.'],
                ],
            ],
            [
                'title' => 'Plants & Trees', 'order' => 2, 'locked' => true,
                'desc'  => 'Tumbuhan dan pepohonan',
                'vocab' => [
                    ['Tree',     'Pohon',   'The tree is very tall.'],
                    ['Flower',   'Bunga',   'She picked a flower.'],
                    ['Grass',    'Rumput',  'The grass is wet.'],
                    ['Leaf',     'Daun',    'The leaf fell to the ground.'],
                    ['Root',     'Akar',    'The root is underground.'],
                    ['Branch',   'Cabang',  'A bird sat on the branch.'],
                    ['Seed',     'Biji',    'Plant the seed in the soil.'],
                    ['Cactus',   'Kaktus',  'A cactus grows in the desert.'],
                    ['Bamboo',   'Bambu',   'Bamboo grows very quickly.'],
                    ['Mushroom', 'Jamur',   'She found a mushroom in the forest.'],
                ],
            ],
            [
                'title' => 'Water & Ocean', 'order' => 3, 'locked' => true,
                'desc'  => 'Air, laut, dan perairan',
                'vocab' => [
                    ['Ocean',    'Samudra',  'The ocean is very deep.'],
                    ['River',    'Sungai',   'The river flows to the sea.'],
                    ['Lake',     'Danau',    'We swam in the lake.'],
                    ['Wave',     'Ombak',    'Big waves hit the shore.'],
                    ['Beach',    'Pantai',   'We went to the beach.'],
                    ['Waterfall','Air terjun','The waterfall is stunning.'],
                    ['Island',   'Pulau',    'She lives on a small island.'],
                    ['Shore',    'Tepi pantai','They walked along the shore.'],
                    ['Tide',     'Pasang surut','High tide flooded the shore.'],
                    ['Coral',    'Terumbu karang','Coral reefs are colorful.'],
                ],
            ],
            [
                'title' => 'Wild Animals', 'order' => 4, 'locked' => true,
                'desc'  => 'Hewan-hewan liar di alam bebas',
                'vocab' => [
                    ['Lion',      'Singa',    'The lion roared loudly.'],
                    ['Bear',      'Beruang',  'The bear is hibernating.'],
                    ['Wolf',      'Serigala', 'The wolf howled at the moon.'],
                    ['Eagle',     'Elang',    'An eagle flew over the mountain.'],
                    ['Deer',      'Rusa',     'A deer ran into the forest.'],
                    ['Fox',       'Rubah',    'The fox is very clever.'],
                    ['Giraffe',   'Jerapah',  'Giraffes have very long necks.'],
                    ['Zebra',     'Zebra',    'Zebras have black and white stripes.'],
                    ['Crocodile', 'Buaya',    'The crocodile waited in the river.'],
                    ['Gorilla',   'Gorila',   'A gorilla is very strong.'],
                ],
            ],
            [
                'title' => 'Earth & Land', 'order' => 5, 'locked' => true,
                'desc'  => 'Bentang alam dan permukaan bumi',
                'vocab' => [
                    ['Mountain',  'Gunung',   'We climbed the mountain.'],
                    ['Valley',    'Lembah',   'The valley is very green.'],
                    ['Desert',    'Gurun',    'The desert is very hot.'],
                    ['Forest',    'Hutan',    'The forest is full of animals.'],
                    ['Cave',      'Gua',      'They explored the dark cave.'],
                    ['Hill',      'Bukit',    'We walked up the hill.'],
                    ['Plain',     'Dataran',  'The plain stretches for miles.'],
                    ['Volcano',   'Gunung berapi','The volcano erupted.'],
                    ['Canyon',    'Ngarai',   'The canyon is very deep.'],
                    ['Cliff',     'Tebing',   'She stood at the edge of the cliff.'],
                ],
            ],
        ];

        foreach ($lessonsUnit3 as $data) {
            $lesson = Lesson::create([
                'unit_id'     => $unit3->id,
                'title'       => $data['title'],
                'description' => $data['desc'],
                'order'       => $data['order'],
                'is_locked'   => $data['locked'],
                'xp_reward'   => 50,
            ]);
            $this->seedVocabularies($lesson->id, $data['vocab']);
        }

        // ══════════════════════════════════════════════════════════════════
        // UNIT 4 — Technology Words
        // ══════════════════════════════════════════════════════════════════
        $unit4 = Unit::create([
            'title'       => 'Technology Words',
            'description' => 'Istilah teknologi dan dunia digital',
            'icon'        => '💻',
            'order'       => 4,
            'is_locked'   => true,
        ]);

        $lessonsUnit4 = [
            [
                'title' => 'Devices', 'order' => 1, 'locked' => false,
                'desc'  => 'Perangkat teknologi sehari-hari',
                'vocab' => [
                    ['Laptop',    'Laptop',      'I use a laptop for work.'],
                    ['Phone',     'Ponsel',       'She answered her phone.'],
                    ['Tablet',    'Tablet',       'He read the news on a tablet.'],
                    ['Keyboard',  'Keyboard',     'Type using the keyboard.'],
                    ['Mouse',     'Mouse',        'Click the mouse to select.'],
                    ['Monitor',   'Monitor',      'The monitor is very bright.'],
                    ['Printer',   'Printer',      'Print the document using a printer.'],
                    ['Speaker',   'Speaker',      'The speaker plays music loudly.'],
                    ['Camera',    'Kamera',       'She took a photo with a camera.'],
                    ['Headphone', 'Headphone',    'He listened with headphones.'],
                ],
            ],
            [
                'title' => 'Internet Terms', 'order' => 2, 'locked' => true,
                'desc'  => 'Istilah-istilah dalam dunia internet',
                'vocab' => [
                    ['Browser',   'Browser',      'Open a browser to surf the web.'],
                    ['Download',  'Unduh',         'Download the file first.'],
                    ['Upload',    'Unggah',        'Upload your photo here.'],
                    ['Password',  'Kata sandi',    'Keep your password secret.'],
                    ['Email',     'Surel',         'Send me an email.'],
                    ['Website',   'Situs web',     'Visit our website for more info.'],
                    ['Search',    'Cari',          'Search for the answer online.'],
                    ['Network',   'Jaringan',      'The network is down.'],
                    ['Wi-Fi',     'Wi-Fi',         'Connect to the Wi-Fi network.'],
                    ['Virus',     'Virus',         'A virus infected the computer.'],
                ],
            ],
            [
                'title' => 'Software', 'order' => 3, 'locked' => true,
                'desc'  => 'Aplikasi dan perangkat lunak',
                'vocab' => [
                    ['Application','Aplikasi',   'Install the application first.'],
                    ['Update',     'Perbarui',    'Update the software regularly.'],
                    ['Install',    'Pasang',      'Install the program on your PC.'],
                    ['Delete',     'Hapus',       'Delete the file permanently.'],
                    ['Save',       'Simpan',      'Save your work often.'],
                    ['File',       'Berkas',      'Open the file on your computer.'],
                    ['Folder',     'Folder',      'Create a new folder for your files.'],
                    ['Backup',     'Cadangan',    'Always backup your important data.'],
                    ['Crash',      'Error/Crash', 'The app crashed unexpectedly.'],
                    ['Settings',   'Pengaturan',  'Open the settings menu.'],
                ],
            ],
            [
                'title' => 'Social Media', 'order' => 4, 'locked' => true,
                'desc'  => 'Istilah media sosial populer',
                'vocab' => [
                    ['Post',      'Unggahan',    'She posted a photo online.'],
                    ['Like',      'Suka',        'He liked the post.'],
                    ['Share',     'Bagikan',     'Share this article with friends.'],
                    ['Comment',   'Komentar',    'Leave a comment below.'],
                    ['Follow',    'Ikuti',       'Follow us on social media.'],
                    ['Story',     'Cerita',      'She posted a story today.'],
                    ['Profile',   'Profil',      'Update your profile photo.'],
                    ['Hashtag',   'Tagar',       'Use a hashtag to reach more people.'],
                    ['Trending',  'Viral/Tren',  'This topic is trending now.'],
                    ['Notification','Notifikasi','You have a new notification.'],
                ],
            ],
            [
                'title' => 'Programming', 'order' => 5, 'locked' => true,
                'desc'  => 'Istilah dasar dalam pemrograman',
                'vocab' => [
                    ['Code',      'Kode',         'Write the code carefully.'],
                    ['Bug',       'Bug/Error',     'There is a bug in the program.'],
                    ['Function',  'Fungsi',        'Call the function correctly.'],
                    ['Variable',  'Variabel',      'Declare a variable first.'],
                    ['Loop',      'Perulangan',    'Use a loop to repeat the task.'],
                    ['Array',     'Array/Larik',   'Store data in an array.'],
                    ['Database',  'Basis data',    'Connect to the database.'],
                    ['Output',    'Keluaran',      'Print the output to the screen.'],
                    ['Input',     'Masukan',       'Get input from the user.'],
                    ['Debug',     'Debug',         'Debug the code to fix errors.'],
                ],
            ],
        ];

        foreach ($lessonsUnit4 as $data) {
            $lesson = Lesson::create([
                'unit_id'     => $unit4->id,
                'title'       => $data['title'],
                'description' => $data['desc'],
                'order'       => $data['order'],
                'is_locked'   => $data['locked'],
                'xp_reward'   => 50,
            ]);
            $this->seedVocabularies($lesson->id, $data['vocab']);
        }

        // ── 4. USER PROGRESS ─────────────────────────────────────────────
        UserProgress::create([
            'user_id'      => $user->id,
            'lesson_id'    => $greetingsLesson->id,
            'is_completed' => true,
            'score'        => 900,
            'time_spent'   => 45,
            'attempts'     => 1,
        ]);

        UserProgress::create([
            'user_id'      => $user->id,
            'lesson_id'    => $colorsLesson->id,
            'is_completed' => true,
            'score'        => 800,
            'time_spent'   => 52,
            'attempts'     => 2,
        ]);

        // ── 5. USER XP LOG ───────────────────────────────────────────────
        UserXpLog::create([
            'user_id'    => $user->id,
            'xp_gained'  => 50,
            'activity'   => 'Selesai lesson: Greetings',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        UserXpLog::create([
            'user_id'    => $user->id,
            'xp_gained'  => 50,
            'activity'   => 'Selesai lesson: Numbers & Colors',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ── HELPER: seed vocabularies ─────────────────────────────────────────
    private function seedVocabularies(int $lessonId, array $items): void
    {
        foreach ($items as [$word, $meaning, $example]) {
            Vocabulary::create([
                'lesson_id'        => $lessonId,
                'word'             => $word,
                'meaning'          => $meaning,
                'example_sentence' => $example,
                'image_url'        => null,
            ]);
        }
    }
}