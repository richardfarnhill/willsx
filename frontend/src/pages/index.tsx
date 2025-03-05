import { GetStaticProps } from 'next';
import Head from 'next/head';
import Link from 'next/link';
import Image from 'next/image';
import styles from '@/styles/Home.module.scss';

export default function Home({ content }: { content: any }) {
  return (
    <>
      <Head>
        <title>WillsX - Professional Will Writing Services</title>
        <meta name="description" content="WillsX provides professional will writing and LPA services for England and Wales." />
        <link rel="icon" href="/favicon.ico" />
      </Head>

      <main className={styles.main}>
        <section className={styles.hero}>
          <div className={styles.container}>
            <h1>Protect your loved ones with a professionally written will</h1>
            <p>
              Our simple online process makes it easy to create a legally binding will
              tailored to your specific needs and circumstances.
            </p>
            <div className={styles.cta}>
              <Link href="/will-instructions" className={styles.primaryBtn}>
                Start Your Will Online
              </Link>
              <Link href="/book-appointment" className={styles.secondaryBtn}>
                Book Video Consultation
              </Link>
            </div>
          </div>
        </section>

        <section className={styles.services}>
          <div className={styles.container}>
            <h2>Our Services</h2>
            <div className={styles.serviceGrid}>
              <div className={styles.serviceCard}>
                <div className={styles.serviceIcon}>
                  <Image 
                    src="/icons/will.svg" 
                    alt="Will Writing" 
                    width={64} 
                    height={64} 
                  />
                </div>
                <h3>Will Writing</h3>
                <p>Create a legally binding will to protect your assets and loved ones.</p>
              </div>
              <div className={styles.serviceCard}>
                <div className={styles.serviceIcon}>
                  <Image 
                    src="/icons/lpa.svg" 
                    alt="Lasting Power of Attorney" 
                    width={64} 
                    height={64} 
                  />
                </div>
                <h3>Lasting Power of Attorney</h3>
                <p>Appoint someone you trust to make decisions on your behalf.</p>
              </div>
              <div className={styles.serviceCard}>
                <div className={styles.serviceIcon}>
                  <Image 
                    src="/icons/consultation.svg" 
                    alt="Expert Consultation" 
                    width={64} 
                    height={64} 
                  />
                </div>
                <h3>Expert Consultation</h3>
                <p>Video consultations with our experienced legal advisors.</p>
              </div>
            </div>
          </div>
        </section>

        <section className={styles.process}>
          <div className={styles.container}>
            <h2>How It Works</h2>
            <div className={styles.steps}>
              <div className={styles.step}>
                <div className={styles.stepNumber}>1</div>
                <h3>Provide Your Details</h3>
                <p>Complete our simple online form with your personal information.</p>
              </div>
              <div className={styles.step}>
                <div className={styles.stepNumber}>2</div>
                <h3>Review & Confirm</h3>
                <p>Review your will and make any necessary adjustments.</p>
              </div>
              <div className={styles.step}>
                <div className={styles.stepNumber}>3</div>
                <h3>Receive Your Documents</h3>
                <p>Your professionally prepared will is ready to sign.</p>
              </div>
            </div>
          </div>
        </section>
      </main>
    </>
  );
}

export const getStaticProps: GetStaticProps = async () => {
  // In a production implementation, fetch homepage content from WordPress API
  const content = {
    // Placeholder content
  };
  
  return {
    props: {
      content
    },
    revalidate: 60 * 60, // Revalidate once per hour
  };
};
