=== Mobile Bonus List ===
Contributors: Time SEO Agencija
Tags: bonus, casino, betting, mobile, shortcode, ajax, türkçe, turkish
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Bahis/casino siteleri için bonus listelerini yönetmek ve görüntülemek için mobil öncelikli tasarımlı WordPress eklentisi.

== Açıklama ==

Mobile Bonus List, bahis ve casino web siteleri için özel olarak tasarlanmış kapsamlı bir WordPress eklentisidir. Sadece mobil cihazlarda görünen güzel, mobil öncelikli bir arayüzle bonus tekliflerini kolayca yönetmenizi ve görüntülemenizi sağlar.

= Ana Özellikler =

* **Özel Yazı Türü**: Kolay yönetim için özel "Bonus" yazı türü
* **Sadece Mobil Görünüm**: 768px altındaki cihazlarda görünen responsive tasarım
* **AJAX Filtreleme**: Sayfa yenilenmeden gerçek zamanlı arama ve kategori filtreleme
* **Modern UI**: Modern uygulama benzeri deneyim için koyu tema ve turuncu vurgular (#f7931e)
* **SEO Dostu**: Tüm bonus linkleri rel="nofollow" özelliği içerir
* **Elementor Uyumlu**: Elementor sayfa oluşturucu ile sorunsuz çalışır
* **Performans Optimize**: CSS ve JS sadece shortcode mevcut olduğunda yüklenir
* **Çoklu Şablon**: Dikey liste, slider ve accordion görünüm seçenekleri
* **Renk Özelleştirme**: Admin panelden frontend renklerini ayarlayabilme
* **Otomatik Tarih Kontrolü**: Başlangıç ve bitiş tarihlerine göre otomatik göster/gizle

= Bonus Yönetimi =

Her bonus girişi şunları içerir:
* Site adı (başlık)
* Logo yükleme
* Bonus açıklama metni (örn: "1000₺ Hoşgeldin Bonusu")
* Bonus linki (yeni sekmede nofollow ile açılır)
* Kategori ataması (Trend, Önerilen veya Tümü)
* Sıra numarası (1., 2., 3. sıraya göre sıralama)
* Başlangıç ve bitiş tarihleri
* Aylık ödeme tutarı (sadece admin panelde görünür)

= Frontend Görünümü =

Eklenti `[bonus_list]` shortcode'u sağlar ve şunları görüntüler:
* Üst ve alt duyuru barları
* Arama işlevselliği
* Kategori filtre butonları (Tümü, Trend, Önerilen)
* Logo ve eylem butonları ile responsive bonus kartları

= Şablon Seçenekleri =

* **Dikey Liste**: `[bonus_list template="vertical"]` (varsayılan)
* **Slider**: `[bonus_list template="slider"]` (kaydırılabilir carousel)
* **Accordion**: `[bonus_list template="accordion"]` (açılır/kapanır liste)

= Stil Özellikleri =

* Arka plan rengi: #0b1224 (koyu mavi) - özelleştirilebilir
* Kart arka planı: #1d2236 (açık mavi) - özelleştirilebilir
* Ana vurgu: #f7931e (turuncu) - özelleştirilebilir
* Tam responsive mobil uygulama tarzı arayüz
* Yumuşak animasyonlar ve hover efektleri

= Admin Dashboard =

* Toplam bonus sayısı istatistikleri
* Aktif bonuslar sayısı
* Süresi bitmiş bonuslar
* Yaklaşan başlangıç tarihleri
* Toplam aylık ödeme tutarı
* Görsel olarak güzel WordPress native stili

== Kurulum ==

1. Eklenti dosyalarını `/wp-content/plugins/mobile-bonus-list` dizinine yükleyin veya WordPress eklenti ekranından doğrudan kurun.
2. Eklentiyi WordPress'teki 'Eklentiler' ekranından etkinleştirin.
3. Bonus girişleri eklemeye başlamak için WordPress yöneticinizde 'Bonuslar'a gidin.
4. Bonus listesini görüntülemek istediğiniz herhangi bir sayfada veya yazıda `[bonus_list]` shortcode'unu kullanın.

== Kullanım ==

= Bonus Ekleme =

1. WordPress yöneticinizde **Bonuslar > Yeni Ekle**'ye gidin
2. Site adını başlık olarak girin
3. Logo yükleme alanını kullanarak logo yükleyin
4. Bonus metni alanına bonus açıklamasını girin
5. Bonus linki alanına bonus URL'sini ekleyin
6. Uygun kategorileri seçin (Trend, Önerilen veya her ikisi)
7. Sıra numarasını belirleyin
8. İsteğe bağlı olarak başlangıç ve bitiş tarihlerini ayarlayın
9. Aylık ödeme tutarını girin (sadece kayıt için)
10. Bonusu yayınlayın

= Bonusları Görüntüleme =

Bonus listesinin görünmesini istediğiniz herhangi bir sayfaya, yazıya veya Elementor widget'ına `[bonus_list]` shortcode'unu ekleyin. Liste sadece mobil cihazlarda (ekran genişliği < 768px) görünür olacaktır.

Şablon seçenekleri:
* `[bonus_list]` - Varsayılan dikey liste
* `[bonus_list template="slider"]` - Slider görünümü
* `[bonus_list template="accordion"]` - Accordion görünümü

= Elementor Entegrasyonu =

1. Elementor sayfanıza bir Metin Editörü widget'ı ekleyin
2. `[bonus_list]` shortcode'unu ekleyin
3. Bonus listesi tam işlevsellikle render edilecektir

= Renk Özelleştirme =

1. **Bonus Yönetimi > Ayarlar**'a gidin
2. İstediğiniz renkleri seçin:
   - Arka plan rengi
   - Kart arka plan rengi
   - Ana renk (butonlar)
   - Yazı rengi
   - Buton yazı rengi
3. Ayarları kaydedin

== Sık Sorulan Sorular ==

= Bonus listesi neden masaüstünde görünmüyor? =

Bu eklenti özel olarak sadece mobil kullanıcılar için tasarlanmıştır. Bonus listesi, odaklanmış bir mobil deneyim sağlamak için tasarım gereği masaüstü cihazlarda (ekran genişliği ≥ 768px) gizlidir.

= Renkleri özelleştirebilir miyim? =

Evet! Admin panelden **Bonus Yönetimi > Ayarlar** bölümünden tüm renkleri özelleştirebilirsiniz.

= Nasıl daha fazla kategori eklerim? =

Eklenti varsayılan olarak "Trend" ve "Önerilen" kategorileri ile gelir. WordPress yöneticinizde **Bonuslar > Kategoriler**'e giderek daha fazla kategori ekleyebilirsiniz.

= Bonus linkleri SEO dostu mu? =

Evet, tüm bonus linkleri otomatik olarak affiliate/promosyon linklerinin uygun SEO işlenmesi için `rel="nofollow"` ve `target="_blank"` özelliklerini içerir.

= Bu önbellekleme eklentileri ile çalışır mı? =

Evet, eklenti çoğu önbellekleme eklentisi ile uyumludur. AJAX işlevselliği, önbelleğe alınmış sayfaları etkilemeden dinamik içerik güncellemeleri sağlar.

= Bonuslar otomatik olarak gösterilip gizlenebilir mi? =

Evet! Her bonus için başlangıç ve bitiş tarihleri ayarlayabilirsiniz. Bonuslar belirlenen tarihlerde otomatik olarak görünür/gizlenir.

== Ekran Görüntüleri ==

1. Arama ve filtre işlevselliği ile mobil bonus listesi görünümü
2. Yeni bonus ekleme için admin arayüzü
3. Bonus kategorileri yönetimi
4. Dashboard istatistikleri
5. Renk özelleştirme ayarları
6. Slider şablonu görünümü
7. Accordion şablonu görünümü
8. Elementor entegrasyon örneği

== Değişiklik Günlüğü ==

= 1.0.0 =
* İlk sürüm
* Bonuslar için özel yazı türü
* Mobil öncelikli responsive tasarım
* AJAX arama ve filtreleme
* Çoklu şablon desteği (dikey, slider, accordion)
* Elementor uyumluluğu
* SEO dostu bonus linkleri
* Performans optimizasyonları
* Türkçe dil desteği
* Admin dashboard istatistikleri
* Renk özelleştirme paneli
* Otomatik tarih kontrolü
* Örnek bonus verileri

== Yükseltme Bildirimi ==

= 1.0.0 =
Mobile Bonus List eklentisinin ilk sürümü.

== Geliştirici Notları ==

= Hook'lar ve Filtreler =

Eklenti geliştiriciler için çeşitli hook'lar sağlar:

* `mbl_bonus_card_html` - Bonus kartı HTML çıktısını filtrele
* `mbl_search_query_args` - Arama için WP_Query argümanlarını filtrele
* `mbl_ajax_response` - AJAX yanıt verilerini filtrele

= Özel Stil =

Görünümü özelleştirmek için temanızda bu CSS sınıflarını geçersiz kılın:

* `.mobile-bonus-list-container` - Ana konteyner
* `.bonus-card` - Bireysel bonus kartları
* `.filter-btn` - Kategori filtre butonları
* `.bonus-button` - Bonus eylem butonları
* `.bonus-slider` - Slider şablonu
* `.bonus-accordion` - Accordion şablonu

= CSS Değişkenleri =

Renkleri özelleştirmek için CSS değişkenlerini kullanın:

* `--mbl-bg-color` - Arka plan rengi
* `--mbl-card-bg` - Kart arka plan rengi
* `--mbl-primary` - Ana renk
* `--mbl-text` - Yazı rengi
* `--mbl-btn-text` - Buton yazı rengi

= Performans =

Eklenti WordPress en iyi uygulamalarını takip eder:
* Koşullu varlık yükleme
* Uygun nonce doğrulama
* Temizlenmiş girdiler ve kaçırılmış çıktılar
* Optimize edilmiş veritabanı sorguları
* Mobil öncelikli responsive tasarım

== Destek ==

Destek ve özellik istekleri için Time SEO Agencija ile iletişime geçin: https://www.timeseoagencija.me

== Yazar ==

Bu eklenti Time SEO Agencija tarafından geliştirilmiştir.
Web sitesi: https://www.timeseoagencija.me
