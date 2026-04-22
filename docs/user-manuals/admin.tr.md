---
title: "Biz Buradayiz"
subtitle: "Yönetici İşletim Kılavuzu"
author: "Biz Buradayiz"
date: "Nisan 2026"
lang: tr
---

# Yönetici İşletim Kılavuzu

**Biz Buradayiz** — Türkçe · Nisan 2026 · Yönetim ve operasyon ekipleri için (programlama kılavuzu değildir).

Bu kılavuz sistemin ne yaptığını, **kamu arama deneyiminin** nasıl çalıştığını ve yönetim panelindeki ana bölümlerin birbiriyle nasıl ilişkili olduğunu açıklar.

---

## 1. Bu sistem nedir?

Biz Buradayiz, **yol yardımı ve çekici** türü hizmet arayan **ziyaretçileri** **hizmet sağlayıcılarla** buluşturur. Ürünün üç ana yüzü vardır:

- **Kamu web sitesi** — Anasayfa, sağlayıcı araması, sağlayıcı profilleri, blog, statik sayfalar, iletişim.
- **Sağlayıcı portalı** — Hizmet sağlayıcıları hesaplarını yönetir, abonelikleri ve çağrı hareketini görür, ücretli paket talep eder.
- **Yönetim paneli** — Ekibiniz kataloğu yapılandırır, kullanıcıları onaylar, yorumları moderasyon eder, site ayarlarını düzenler ve hareketleri izler.

Uygulama arkasında modüller halinde (kullanıcılar, platform, coğrafya, içerik, telefon, yapılandırma vb.) organize edilmiştir. Paneli kullanmak için modül adlarını bilmeniz gerekmez; sadece sorumlulukların nasıl gruplandığını göstermek için anılırlar.

---

## 2. Oturum açma, dil ve yetkiler

- Yöneticiler **yönetici giriş** adresinden oturum açar (yerelleştirilmiş yol, örneğin `/tr/admin/...`).
- Arayüz **birden çok dilde** çalışabilir; dil değişimi etiketleri ve yapılandırmaya bağlı olarak içeriği etkiler.
- **Roller ve izinler**, her yöneticinin hangi menü ve işlemleri görebileceğini belirler. Bir bölümü göremiyorsanız rolünüzde o izin olmayabilir. Erişim için süper yöneticiyle iletişime geçin.

---

## 3. Kontrol paneli

**Kontrol paneli** operasyonel göstergelerin özetidir. Tipik kutucuklar:

- **Aktif paket abonelikleri** — Durumu **Aktif** olan abonelikler (tüm geçmiş abonelikler değil).
- **Ödenen abonelik geliri** — Ödeme kayıtlarına bağlı gelir (kesin kurallar muhasebe sürecinize bağlıdır).
- **Sağlayıcılara gelen çağrılar** — Sağlayıcılar için kaydedilen **gelen çağrı** olayları (**Verimor çağrı olayları** bölümüne bakın).
- **Yorumlar** — Gönderilen toplam yorum, **ortalama puan** (yalnızca onaylı yorumlar, 1–5) ve **onay bekleyen** yorum sayısı.

Ekranda sunulduğunda **tarih aralığı** ile süzme genellikle mümkündür.

---

## 4. Kullanıcı yönetimi

### 4.1 Yöneticiler

**Yöneticiler**, yönetim paneli için personel hesaplarıdır. İzinlerinize göre oluşturma, düzenleme, devre dışı bırakma veya geri yükleme yapabilirsiniz. Yöneticiler, son kullanıcı müşterilerinden ve hizmet sağlayıcılarından ayrıdır.

### 4.2 Müşteriler ve hizmet sağlayıcıları

**Müşteriler** ve **hizmet sağlayıcıları**, farklı **tiplerde** **kullanıcı** kayıtlarıdır.

- **Hizmet sağlayıcılarının** ek verileri vardır: sundukları **hizmet**, **şehir** (dolayısıyla bölge), isteğe bağlı **profil görseli**, **kamu profil kısa adresi** ve **paket abonelikleri** ile **çağrı geçmişi** bağlantıları.
- Bir hizmet sağlayıcısını **etkinleştirmek** kritiktir: **durum** **Aktif** olana kadar genellikle **sağlayıcı portalına giriş yapamazlar**.

Bir hizmet sağlayıcısı **ilk kez Aktif** yapıldığında sistem tipik olarak:

1. Daha önce yoksa **`approved_at`** (onay zamanı) alanını doldurur.
2. İlgili **hizmet** için tanımlı **ücretsiz kademeli paket** varsa, **bir kez** **hoş geldin ücretsiz paketi** verebilir.
3. **Arama sıralama puanlarının** arka planda **yeniden hesaplanmasını** tetikler (Bölüm 10).

### 4.3 Bekleyen kayıtlar

Kamu sitesinden **kendi kendine kayıt** olan sağlayıcılar genelde **beklemede** oluşturulur. Ekibiniz uygun gördüğünde **inceleyip etkinleştirmelidir**.

---

## 5. Bölge yönetimi (coğrafya)

**Ülkeler**, **eyalet/il (states)** ve **şehir/ilçe (cities)** şunları besler:

- Sağlayıcı **konumu** (profilde).
- Kamu **sağlayıcı arama** sayfasındaki süzgeçler (hizmet + il ve/veya ilçe).

Doğru ve gerektiğinde çevrilmiş tutun; ziyaretçi ve sağlayıcılar tutarlı yer adları görür.

---

## 6. Platform yönetimi

### 6.1 Hizmetler

**Hizmetler**, sağlayıcıların sunduğu iş türleridir (örneğin yol yardımı kategorileri). **Kamu arama süzgeçlerinde** görünüp görünmeyeceği gibi alanlar vardır. Hizmetler **paketlere** bağlanır.

### 6.2 Paketler

**Paketler**, sağlayıcının satın aldığı teklifi tanımlar: fiyatlandırma, fatura dönemi, **ücretsiz kademe** olup olmadığı, **sıralama**, popülerlik işaretleri ve hangi **hizmetlerin** o paketi kullanabileceği.

### 6.3 Paket abonelikleri

**Paket aboneliği**, bir **sağlayıcıyı** bir **paket anlık görüntüsüne** bağlar ve şunları izler:

- **Durum** (örneğin aktif veya ödeme bekliyor).
- **Ödeme durumu** ve **ödeme yöntemi** (örneğin doğrulama bekleyen havale).
- **Başlangıç / bitiş** tarihleri (bazı paketlerde bitiş olmayabilir).
- **Kalan bağlantılar** — Kamu aramada aboneliğin **aktif** sayılması için **sıfırdan büyük** olması gerekir.

Ekibiniz iç süreçlerinize göre abonelik oluşturabilir, banka ödemelerini onaylayabilir, durumu düzenleyebilir ve **yönetici notları** ekleyebilir.

### 6.4 Verimor çağrı olayları

**Verimor çağrı olayları**, bir sağlayıcıyla ilişkilendirilmiş **telefon aktivitesi** kayıtlarıdır (örneğin gelen aramalar). Kullanım alanları:

- Yönetim ve sağlayıcı panelinde **operasyonel görünürlük**.
- **Sıralama puanı** formülündeki **aktivite** bileşeni (Bölüm 10).
- **Yorum doğrulama** — müşteri, numarası o sağlayıcıya giden **cevaplanmış gelen bir aramayla** eşleşmedikçe yorum gönderemez (Bölüm 9).

---

## 7. Yorum yönetimi

**Yorumlar** **beklemede** veya **onaylı** (ve yapılandırmaya göre başka durumlar) olabilir.

- **Onaylı** yorumlar, profilde görünen **ortalama puanı** ve sıralama girdilerini etkilemelidir.
- Yorum oluşturulduğunda, güncellendiğinde veya silindiğinde sistem sağlayıcı **puan özetlerini** günceller ve **sıralama yeniden hesaplamasını** kuyruğa alır.

Moderatörleri **içerik politikası** konusunda eğitin (metinde ne kabul edilir, anlaşmazlıklar, ret kararları).

---

## 8. SEO yönetimi

**SEO kayıtları**, önemli kamu URL’leri için **meta başlık ve açıklama** (ve ilgili alanlar) yönetmenizi sağlar; arama motorlarında özet görünümü iyileştirir. Metinleri pazarlama ve hukuk çerçevesinde tutun.

---

## 9. Müşteri yorumlarının gerçek aramaya bağlanması

Sahte yorumu azaltmak için **kamu yorum gönderimi** **doğrulanmış arama** gerektirir:

- Müşteri **telefon numarası** girer.
- Sistem, **gelen**, **cevaplanmış** bir çağrıyı o **sağlayıcı** için arar; **arayan numara** eşleşir (normalize edilmiş) ve o çağrı **başka bir yoruma bağlı değildir**.
- Eşleşme yoksa gönderim doğrulama mesajıyla reddedilir.

Yorum **beklemede** saklanır; personel **onaylayana** kadar politikanıza göre yayınlanmaz. Destek ekibine bu kuralı öğretin ki sağlayıcı sorularına tutarlı cevap verilsin.

---

## 10. Kamu sağlayıcı araması — görünürlük, öne çıkan alan ve sıralama

Bu bölüm **ziyaretçinin gördüğü davranışı** ve **ayarlarda kontrol ettiklerinizi** açıklar.

### 10.1 Aramada kimler listelenir?

Kamu arama sonuçlarında yalnızca **tümü** doğruysa görünen sağlayıcılar vardır:

- Hesap tipi **hizmet sağlayıcısı**.
- Hesap **durumu** **Aktif**.
- En az bir **aktif paket aboneliği**: durum **Aktif**, **ödeme durumu Ödendi**, **kalan bağlantı > 0** ve abonelik **süresi dolmamış** (bitiş tarihi varsa gelecekte olmalı; bazı paketlerde bitiş olmayabilir).

Bunlardan biri bozulursa sağlayıcı **kamu aramasında görünmez**; düzelene kadar.

### 10.2 Ziyaretçi süzgeçleri

Ziyaretçiler sonuçları şununla daraltır:

- **Hizmet** (isteğe bağlı).
- **İl (state)** ve/veya **ilçe (city)** (isteğe bağlı).

**İlçe** seçilirse sonuçlar o ilçeye göre filtrelenir. Yalnız **il** seçilirse, o ile bağlı şehirlerdeki sağlayıcılar gelir. Bu sayfada **serbest metin anahtar kelime araması** yoktur; yapılandırılmış süzgeçler kullanılır.

### 10.3 Sayfalama

Sonuçlar **sayfalanır** (sayfa başına sabit sağlayıcı sayısı). Tipik düzen: öne çıkan alandan sonra sayfa başına **12** sağlayıcı.

### 10.4 Öne çıkan sağlayıcılar — yalnızca 1. sayfa

Yalnız **1. sayfada**, ana listenin üstünde **öne çıkan** şerit görünebilir.

- **Kaç slot?** **Öne çıkan sağlayıcı sayısı** ayarı (yazılımda varsayılan sıkça **3**).
- **Kim seçilir?**
  1. **Yeni sağlayıcı saati** sıfırdan büyükse: sistem önce **`approved_at`** zamanı son **N saat** içinde olan sağlayıcılarla dener; **en yeni onay** önce gelir. Bu gruptan **tüm slotları** dolduracak kadar kişi varsa, öne çıkan şeritte **yalnızca** bu yeni sağlayıcılar gösterilir.
  2. Yeni sağlayıcı yeterli değilse veya pencere kapalıysa: aynı süzülmüş havuzdan **`ranking_score`** en yüksek olanlarla doldurulur.
- **Yinelenen yok:** 1. sayfada **öne çıkan** gösterilen sağlayıcılar, aynı sayfadaki normal listeden **çıkarılır** (iki kez görünmezler).

Öne çıkan **2. ve sonraki sayfalarda tekrarlanmaz**.

### 10.5 Ana liste sırası (öne çıkanın altında)

Öne çıkan işlendikten sonra normal liste şöyle sıralanır:

1. **`ranking_score`** — **yüksekten düşüğe**.
2. **Ad**, sonra **soyad** — alfabetik eşitlik kırıcı.

### 10.6 `ranking_score` nasıl hesaplanır?

Sistem her **aktif hizmet sağlayıcısı** için sayısal bir **sıralama puanı** tutar. **Arka planda** (anında olmayabilir) şu gibi durumlarda yeniden hesaplanır:

- Yorumlar değişince (oluşturma, düzenleme, silme, geri yükleme).
- Sağlayıcı için yeni **Verimor çağrı olayı** kaydedilince.
- Sağlayıcı **ilk kez aktif** olunca.
- **Sıralamayla ilgili ayarlar** (ağırlıklar, öne çıkan sayısı, yeni sağlayıcı penceresi) kaydedilince.

Formül **görecelidir**: her bileşen, hesaplama anında **tüm aktif sağlayıcılar arasında** **min–maks ile normalize** edilir, sonra ağırlıklarla birleştirilir.

**Bileşenler:**

1. **Değerlendirmeler** — Sağlayıcının saklanan **ortalama yorum puanı** (iç özetleme sonrası **onaylı** yorumlara dayanır).
2. **Aktivite** — O sağlayıcıya bağlı **Verimor çağrı olayı sayısı**.
3. **Deneyim / platform süresi** — **`approved_at`** tarihinden bu yana geçen **gün sayısı**.

**Ağırlıklar** (yönetim doğrulamasında üçlünün toplamı **100’ü geçmemeli**):

- **Sıralama ağırlığı — değerlendirme** (varsayılan sıkça %50).
- **Sıralama ağırlığı — aktivite** (varsayılan sıkça %30).
- **Sıralama ağırlığı — deneyim** (varsayılan sıkça %20).

Üçü de sıfır kaydedilirse yazılım **50 / 30 / 20** varsayılanına döner.

**Önemli:** Puanlar **göreceli** olduğundan, **sizin** sayılarınız aynı kalsa bile başka sağlayıcılar daha çok arama veya daha iyi puan alırsa **sıranız değişebilir**.

---

## 11. Ayarlar

Ayarlar gruplara ayrılmıştır:

- **Genel** — Site geneli temel bilgiler.
- **Sosyal medya** — Temada kullanılan bağlantılar.
- **İletişim bilgisi** — Telefon, e-posta, adres tarzı alanlar.
- **Platform** — Sağlayıcılara gösterilen **banka havalesi talimatları** gibi operasyonel metinler.
- **Medya** — Varlık yapılandırması.
- **Mobil** — Kullanılıyorsa uygulama/anımsatıcı anahtarları.
- **Geliştiriciler** — Teknik entegrasyonlar (hassas).
- **Sağlayıcı sıralaması** — **Öne çıkan sağlayıcı sayısı**, **yeni sağlayıcı süresi (saat)** ve Bölüm 10’daki üç **ağırlık**.

Sıralamayla ilgili anahtarlar kaydedildiğinde **sıralama yeniden hesaplaması** tetiklenir.

---

## 12. İçerik yönetimi (CMS)

### 12.1 İçerikler

**İçerikler**, türü yapılandırmaya bağlı tekil editoryal öğelerdir (makale, statik sayfa…). Başlık, gövde, yayın durumu, SEO uyumlu kısa adres ve uygunsa kategori ve etiket ilişkileri yönetilir.

---

## 13. CRM

### 13.1 İletişim talepleri

**Bize ulaşın** formundan gelen mesajlar. SLA’nıza göre yanıtlayın.

---

## 14. Operasyonel notlar ve sınırlar

- **Sıralama güncellemeleri asenkron** olabilir; büyük değişikliklerden kısa süre sonra güncellenir.
- **Öne çıkan yeni sağlayıcı** davranışı **`approved_at`** ve **saat penceresine** bağlıdır.
- **Paketler ve “bağlantılar”** hem ticari hem teknik kavramlardır; prosedürleri sözleşmelerinizle hizalayın.
- **Telefon entegrasyonu** doğru olmalı ki **çağrı kayıtları** ve **yorum doğrulama** güvenilir kalsın.

---

## Belge kontrolü

| Öğe       | Değer        |
|----------|--------------|
| Ürün     | Biz Buradayiz |
| Hedef kitle | Yöneticiler |
| Dil      | Türkçe       |
| Ay       | Nisan 2026   |

*Kılavuz sonu.*
