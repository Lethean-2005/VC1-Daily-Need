<style>
.dn-contact-hero {
    position: relative;
    min-height: 640px;
    border-radius: 0 0 28px 28px;
    overflow: hidden;
    background: linear-gradient(120deg, #0b3634 0%, #0F5553 55%, #12726e 100%);
    display: flex;
    align-items: flex-end;
    padding: 60px 0 60px;
    font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
}
.dn-contact-hero .container { max-width: 1200px; position: relative; }
.dn-contact-hero-text { max-width: 480px; color: #fff; }
.dn-contact-hero-text h1 { color: #fff; font-size: 2.4rem; font-weight: 800; line-height: 1.25; margin-bottom: 16px; }
.dn-contact-hero-text p { color: rgba(255,255,255,.75); font-size: .95rem; line-height: 1.65; max-width: 420px; }

.dn-contact-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 24px 60px rgba(0,0,0,.25);
    padding: 22px 24px;
    max-width: 360px;
    margin-left: auto;
}
.dn-contact-card h2 { font-size: 1.2rem; font-weight: 800; color: #14110d; line-height: 1.3; margin-bottom: 4px; }
.dn-contact-card .sub { font-size: .78rem; color: #9a9a92; margin-bottom: 14px; font-weight: 600; }
.dn-contact-field { margin-bottom: 10px; }
.dn-contact-field label { display: block; font-size: .7rem; font-weight: 700; color: #9a9a92; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.dn-contact-field input,
.dn-contact-field textarea {
    width: 100%;
    border: 1px solid #eceae4;
    background: #faf9f6;
    border-radius: 5px;
    padding: 8px 12px;
    font-size: .85rem;
    font-family: inherit;
    color: #14110d;
    resize: none;
}
.dn-contact-field input:focus,
.dn-contact-field textarea:focus {
    outline: none;
    border-color: #0F5553;
    background: #fff;
}
.dn-contact-submit {
    width: 100%;
    background: #14110d;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 11px;
    font-weight: 700;
    font-size: .84rem;
    letter-spacing: .02em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s ease-in-out;
}
.dn-contact-submit:hover { background: #0F5553; }

@media (max-width: 991px) {
    .dn-contact-hero { align-items: center; padding: 50px 0; }
    .dn-contact-hero .row { flex-direction: column-reverse; gap: 32px; }
    .dn-contact-card { margin: 0 auto; }
}
</style>

<section class="dn-contact-hero">
    <div class="container">
        <div class="row align-items-end g-4">
            <div class="col-lg-6">
                <div class="dn-contact-hero-text">
                    <h1>Get In Touch<br>With Daily Needs</h1>
                    <p>Questions about an order, a product, or just want to say hello? Send us a message and our team will get back to you shortly.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dn-contact-card">
                    <h2>Send us a message</h2>
                    <div class="sub">Please fill in the details below.</div>
                    <form>
                        <div class="dn-contact-field">
                            <label for="dn-contact-name">Your Name</label>
                            <input type="text" id="dn-contact-name" placeholder="John Doe">
                        </div>
                        <div class="dn-contact-field">
                            <label for="dn-contact-email">Email</label>
                            <input type="email" id="dn-contact-email" placeholder="you@example.com">
                        </div>
                        <div class="dn-contact-field">
                            <label for="dn-contact-subject">Subject</label>
                            <input type="text" id="dn-contact-subject" placeholder="How can we help?">
                        </div>
                        <div class="dn-contact-field">
                            <label for="dn-contact-message">Message</label>
                            <textarea id="dn-contact-message" rows="2" placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" class="dn-contact-submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
