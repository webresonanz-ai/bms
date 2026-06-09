<template>
    <div class="about-page">
        <!-- Hero Section -->
        <div class="hero-section luxury-card mb-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Batavia Madrigal Singers</h1>
                    <p class="lead text-white-50 mb-4">
                        Batavia Madrigal Singers is one of Indonesia's leading vocal ensembles and choirs, having
                        achieved numerous prestigious accomplishments in both the international and Indonesian music
                        scenes. The repertoire performed by BMS spans a wide range of musical eras, from classical to
                        contemporary compositions, and encompasses various styles ranging from Baroque music to modern
                        pop.
                    </p>
                    <div class="stats-row d-flex gap-4">
                        <div class="stat">
                            <h3 class="fw-bold mb-0">50+</h3>
                            <small class="text-white-50">Active Members</small>
                        </div>
                        <div class="stat">
                            <h3 class="fw-bold mb-0">100+</h3>
                            <small class="text-white-50">Performances</small>
                        </div>
                        <div class="stat">
                            <h3 class="fw-bold mb-0">15+</h3>
                            <small class="text-white-50">Years Experience</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTxULfnjzbu0nWV6P-zRBu1uzpcI4EE4iGPtg&s"
                            alt="BMS Choir" class="img-hero-about">
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="luxury-card h-100">
                    <div class="card-icon mb-3">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Mission</h4>
                    <p class="text-white-50">
                        To inspire and enrich our community through outstanding choral performances, fostering musical
                        excellence and creating transformative experiences that celebrate the power of the human voice.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="luxury-card h-100">
                    <div class="card-icon mb-3">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Vision</h4>
                    <p class="text-white-50">
                        To become a leading choir in Indonesia with global recognition, promoting musical excellence,
                        artistic innovation, and positive social impact through the art of choral music.
                    </p>
                </div>
            </div>
        </div>

        <!-- Leadership Team -->
        <div class="luxury-card mb-4">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-people me-2"></i>
                Leadership Team
            </h3>
            <div class="row g-4">
                <div v-for="leader in leadershipTeam" :key="leader.id" class="col-lg-3 col-md-6">
                    <div class="leader-card text-center">
                        <img :src="leader.avatar" :alt="leader.name" class="leader-avatar mb-3">
                        <h5 class="fw-bold mb-1">{{ leader.name }}</h5>
                        <p class="text-primary mb-2">{{ leader.role }}</p>
                        <p class="text-white-50 small">{{ leader.bio }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements -->
        <div class="luxury-card">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-trophy me-2"></i>
                Our Achievements
            </h3>
            <div class="row g-4">
                <div v-for="achievement in achievements" :key="achievement.year" class="col-md-4">
                    <div class="achievement-card">
                        <div class="year-badge">{{ achievement.year }}</div>
                        <h5 class="fw-bold mb-2">{{ achievement.title }}</h5>
                        <p class="text-white-50 mb-0">{{ achievement.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const leadershipTeam = [
    {
        id: 1,
        name: 'Avip Priatna',
        role: 'Art Director/Conductor',
        bio: '15+ years of choral conducting experience',
        avatar: 'data:image/jpeg;base64,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'
    },
    {
        id: 2,
        name: 'Aksa Syadri',
        role: 'Choir Coordinator',
        bio: 'Coordinates rehearsals and manages singers schedules',
        avatar: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFHCQZKT6yQYOfIYNqKyaF2r7Sp22B_G9gJw&s'
    },
    {
        id: 3,
        name: 'Elisabet Arum Trihapsari',
        role: 'Soprano Coordinator',
        bio: 'Experienced soprano with a passion for vocal technique and performance',
        avatar: 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAMAAzAMBIgACEQEDEQH/xAAcAAACAwEBAQEAAAAAAAAAAAAEBQIDBgcBAAj/xAA8EAACAQMDAgMFBwMDBAIDAAABAgMABBEFEiExQRNRYQYUInGBIzKRobHB8BVC0QdS8TNisuFykhYkNP/EABkBAAMBAQEAAAAAAAAAAAAAAAECAwAEBf/EACARAAMBAAMBAQADAQAAAAAAAAABAhEDEiExQTJRYSL/2gAMAwEAAhEDEQA/ANHYxwXEXvj3J4OcqTkN+ua0lpKk0AdU69RXPdAufd74Idu2Qf3DoeoNbfTZpnLrtZxwVYrgVy/DpraWjKeFLiIxlRg9/L1rHe2GhyGKCSDGEbAYklgOPPzrS3GqRWrENgkHkeVV2lxFq8EsU67sHgDIyPpTEjn+laPq9xNNLI2VjAUK74DE84Hmcd6d2eqKbRrHVE8SIDaruuWiI/b0ph4UnuZghQwuHOZf9x+uSKstPZIbA89wW3AFlZSefnn+elVVLMYrRYttPppiWZ1eU7tggGDgDpz6UPrWrtDZRRRiW3upEDlgfiB6HcPxA/LpT25WfezB4zsYAFYSuMdf7v5isPfw3kl411qDBTImQyxfd4wMgn+dakxkgeeW/NoqSRyKrOZSWU4fpz69AP8AmpywTzQeM0MswnTw0CpvAwQcDj+c1Vc3M06opkLCMBRyQGA6HHQfQUdHrc8RheVWwo5wMBjg4YeRpE1o7RDXL6K6022tWthH4A2ujxfcIHPJHehINIQadHqCotvGH3EpgtIAey96fWerWr2K+DZrIwnUyOwDYIw27B5J4qGtXsdlDG12omuMAR2wZkxyCTle/wClMhGxUmqSe9yG7UhZZPiJGGUdsj5dh+p50Fm0eoOEmjeNN58CyOclQB8bkdR3C9B356Ye9ne9ujPPHGiZOI0PT1JPX8B1pxo2sLbwiCOMmZ3ZUG5uQ2O+eD59AePWl1aM9w0WryR6VayXUwQxM45I+LJB4GO38PqvWLxbdp4YPfLeWJpHtpFGFxj7wYYGD9eKvu7S71CJXmtykMLBirsQWPGc/wBvn0/HOCAGnuERvCXEG4opjf4TgE/EDncR+w+RYUq0+x8NvCtZZorkqEEUcjMqp5DHVj8fJ6A5x0p1bvYQ2iRq6ZXd9mxJVcHBHHTkdKGumu4LG02lom2A+Iq/HJxycjpnqe3NZ6FZ7aGfYqb53Z5SwKlTngAg9O3eldYFLRnpF5Za3rQe/wDAuZEVkhh8LaeucFe/yNX3+oO0Rhlfw9j4UBQO/Q9+vrSBbWO4dln8MHK5cfCyJgDKkc5+Inr5dhihrme/05I49UU3VogIivFX4wm7HJxzyOpzWfqDnoZdO8gUywwzjfhopAJFztbB2nPPr6fjS1jG0QMWnHwduJDG7xknd1UbhnHHbHlRltJI9l41km+IMC02evcDk+pNfXlyTLh95kjU5ZyeB1PANaX/AGGkC/07RorRX91kZgCybi7vjgDjPY1oLbS0/oNnLp8ASZoY5GjWMbd20csO2f3qGp6Pqem6XBczMXnH32B2EEqThsHJI9MDrS/Qry7itbe9guZH+zCBGj4jwSoGQeRgDqDVBShLjfDc3FvEN9vIGeGQYGO6jt14+mab6XYT6lbGcTe7fEVKbyOfP+eVZ6YT6fqfvF74UyXEguVmEeMc88HofMYFOmnvLRitheK0D/aAw4xz6djWc6bRNp8y/wBTtsOmfGUY3DzrpCXLRyAs0bIegTk1y7SZQNWs8qmPFHGPXGfpWwmkEV2W8XcqsQvYcEj9jWUOh6pSgrUGLM0hg3KTgOT+1EaNPBZkSSSou/IAXmlYeOdiJ5tqE7sAkgH6CoXhC28UcEOQc4kHerLi/CDo1lhNpz/FI0fxZJYtjmrzeym+8OKJWtmTIlLkAEds9KxsJjk8CKXIj4zztrTR3NtDAIUnXGw4DHoP+KFx1Mnp7dX0ciNb+IU3K5Z1wG6nIrLG+gghNtLA1xhyY2m+7jGBnnnz+taO5v7ORSoViZIiY/gHx58if3rETKscSqCGCnGVOR/OtQspK0Y3NrEbp1lGWaIuqW4wu7jAGenB/KgnuJRbRwSELs2gkDcCAc4x+VMLCwe40+S6iuSHRwhiTO5sn4R5fzFUaot00e42SQxlzucDBBA/elwZvPovu5vGvFuZOSMH4Dtz+HWvL27e5ZS0jkEY2t3+tX2mkyytsYkDOAo5/DHpk49D34r7V7T3WRfdyptpVzFKmfiA68+YoYw6iia0s1t4JIN7SFQJS6lQG7nPTHl6davso7G5RbU4gu0YO1yZCyqAcgDjAz06n5Uu8N55GYbncDBbGT+IrQ2eiX1kkhhvrfxXhzKIWDeGueefP6f5or0zNJ4EjQG5vpRLC6ho1+JR/wDUdTyDye1ItUvrZZ4LqUySu4YeDCq4Xy5/t+fPYYNXR6hNqsS2izM7ICFAT4peOBjP+0Hv1HNDTaZcWd5bxNbvM2xytuqgE4Hcg9Mbjx6UX8ESLLW5in0+S6lkMfhqsaAjG4Y6YP8Ab+vnSu1s5CA9xHHGgBAkhBzyM4+o715cuzy+H4SReHkFNvI9PpXu65EcZBfYDtUgHBJ7fOp9tH64iq++2nCrsKDnKY+IdiR25zUkudts8EyB1PdjwOvaiNQ0uKO3cTMxvGlbeEztUZ3bfz/boKUXEk1s4MyPNF03Rrkr8x881mMvSuK3nsS8mlODGzh2tJG+zkPT4fJuf50orSbyHVPaCBIbc24lkQNE/JGXGR69OaHMgmg8eHf4eATmMjGehwautdNa91GGW0lMF7FJuim3Y+IAkc00/wCgpL8Oje16Ld+z91bk5kC+Ii98jn/I+tYD2dvm04TR6lbqIDNuVZXwrduf5iin9ph7R+620++1v0OySNAPtB3x2Py6fSgk0TUkufdbi8ic5EqSyArsYchT15qnb8J4WanNZXVq0EF1AkckjSpG5DNE+cEE57jyz1peJdNtlEMs8MhUcO0mwsPlg48uvatVayzahGYJ7a2efAUyLwA3PK48+p46g1g9fsPctVnt5j8SngnjIPP70yFJB/BljmX78bq6+pBzzWqtftLGzl4y8e5sLt65P71m50AU/COPWtai+HbWsQI+xhVcEdeB1/A0/BrG5QRpoFOJGxjzYD9TV3viCD4bh3jGfuENj8M1TDbia53yoGIzgt26D/NfS3LR3ogTc0CrmRzK5CnOAoHeqvSSR5HcbtipE5V2ADFWx+OKc6paONNt75AwcEBkAzuBoFYWimWS1ghxzl0UAr+Wa1Ito5wbW4nVkeEDCMMHH861K90afhjLx457SwaOV1UoFYg9t3X9Ku1JIvDEcMkTrEWAaMj4xnAPHHQU3/o0UExuY54jGrcLIWJ3eWfTPXtU5RZ+8ySS2doRGwLzEEAnnnHz+v0qVrR4eAfspbst2bq5EiWYQ7WD7QXHp/d0x86lrXtBALzdBDHKShjLsDjseCeR0q+DUrECcM7SwBhtUfAoJ57fX9KVSafp0jC4hZlfdxbBskHn9gPxpcxG3X6ER3ovA91dWJaNsJ4gb4hx94Dj5ZoHxok32sIjaQnftPws3bg/Xmmc2p3Gpzxx2Fuwsw20xRYUnPbjjr3pff6ffhIpLe2kiaWQRxK/BDeZPYY/H1oig1xDPIs1zGGDlmxEFG1Wzk7gOmAf0qq0vbixEgEqIpBy7jhc9sftT3+kSQ2cFpHP41+ihLhw2VKnOAw7AE4Hz+dLLmx3Bt5VCW2lVXdk9+nPn+FbBkwz2V2XplSCbA8RQ8ojOc5429+30xTz2ncQTxzK8ymI/wDUHDcgjJzye/40u0TS0t5IpYrpHeKba6g4JXHb64/A029qJY7qMpE4LuV2jPr+ND8N+mRztUpn4WJYMwBLZ8zX0cjohjEjDfxjP3qvtoonu1iurgwRq6xyOWACknvmvtditYDLBayrNCy842nacefUHvkVNIr2RXKkgUTTbljZiodwTuYcHA+lAG7KH4raVVBwGIPI8xTGG41PWrQbyrC1QIVLAZZePzAz6UJeNPcX3g6czRIQFZ3UsVcgfCvmR+VBoyfhKXUYLC2ls445pI7rDsY7dstg859PXzNLJ9QgR1X3RtrZ3K3Iy3ABz1/91c1lPp0rweAFw320nHxHvljwTREMsFxrEM11LDNGg2tArFmdcY+IevqadAYbZ6F7O3WgyzXd00kw6XYOzwCvQD/bjrg0vTxbbV203Wypa6x4F4qkeIOQACeh48+TUL2dIbiWHRtMWyTcUKCbDHI6EjnbyOM0s1S1VoVjlnnit+JFgUF90h9Og+eATTeMQf2RgsJFM0gW+gcr8TsROueD86Va3Zarq1+11DFaCIjEYeYlsZJ+Lg880m8XUHt43mt7ieGHn3oRsXC+RPkMceVNrfVre7iEm9+Ph4RPn3+db58CDtOssixhGy5C/d861shZpZG7BMcef8NZWxR5dQtQZdwMgbjyH1rVeJtWZ/8Acf04P6V08C/5bE5n6imOVYYpZG4wON3Q5JxXgOYQWOXIycLgAmoh5WeAI4C4ywK5zjBH/kfwoli7AsGAxwVK1QmQR5fDXwyVYKRkjHaitSDwxsGmP3MlSAD0PHHag5d6RtuYkupP4ivroMYpXbHIOc9elJS1hR9a3c9rBhXDeJg4kJI+fWmUOtw3KIl4qxXB+zdyDtbp8XB65/AGk8y7UhTOcDmvILGS7UtEskmNwdYlJZcYwenOcnv2rm5G+3hWZTXo/wBdjkWy8G3h94YbXmMZAA9OevIJyeaSWUl1M5t4lNvdpnwxnLngHdnv/irdG1bULKaS2ls5Z4FARVZtu1ucDkcn4+lM75ItKTx42RbqaMiRUhIXB75J4wMfgfOsKJ4tRdhbMtu1rdxyFXm3dXwcnbgjgdOeTj51Vfavp9nZG1m1SQRg5TeWeRjnq2P0AA5qvUfaB9P024spozdXMsaxwMQPh9WyOTz/ADvTpvs5awYuLtfebuTl3k5APkBTfmjTDp4H6l7Q2xe3e3v7VvgDKd/VeML8wc0ztbpPcLS8tlhaSVXMoQ7vDfscHvikt5Z2MqtHcRQHIOMgAj5VnJYbn2dvI7q3Z5rLcN8e/Bx5UB64nJtYC+pSBLqP3Y7GMzBfvgkZIPUceWKZ6/pqF0vLQo0OxUD/AN3XGPzFLLe6sb21EilmhuJFkjY9W6g5444HIz1WjtSsrWDwo7dUmhMW4mJzy2cjPqcfpSv/AEmBNp+mwyLLLGZHXBZg4CZ6gnnt/BRB0rT5rWe7uPdbmZ8/G6ALn/anB2/z6Ba1di3tJFu7aKYiMLbwICjhj1IA79OpqyygSXTPBvvEtcljlF+APjoARyfxpcG0T3elXTyTxWN0tnCrZMYdpA+FA6k5GDkcHvxxQNompJL4b2kZeQFkMaorR4GSTuH75rQJcG3cxy+FcWkeYoQDsbBOcNx2J4GeevFD3KSRwvf2zpIlsVMzxyA8MSAAO+O/Y/StgExPLO1qNt20sY2hcyMSPMDd36Hv3pzpNxBcXNstvbxsYV3RsrKvOeSem7txX0srNJHdykOGRljc4wVJBIx+FUwiOO4tpNiBRKgcBeNu4ZpdQ+PBzc2091fQyWc4s/GLK1wsBVQcfdVT94+pHavJbSznbTp1t2inkGJX3F2DEYILkknDAj6VpdThaWzeKIL40PxxZbA3ryoz5HofTNYC/wBX1Wy1G0tryBbeCW4EzlcfZu3UDBzjcc7iec546VSfRdwf6Ddf061WMQvM/vE0TIBlSA7KOD5kfkaT6v8A6f6pdXrXGmJa28Evx+AzY8Nu4Hp3+uK1WgxzRaSpuSrXDySSn4cYLuWx5/3c+uenSmyzxSjc+oNGw4KAqMfiKHwDZyXQCkmsRR7s7FZgO/TH71pSB7szZAySflnn96w3+nztLq9zM7M5SHjPz5/StxIri1G0cN5V3RPWSNPWRtlQzsFJO2PB2txnOP2qx8KSFUZPfPWq4S2+RsHoDk/Mn96+DgZycnyomLfEMbHoxIxjGevFX2UNtKjrq0hDfCiohwzHpnPT/Hel7Ekk8qeBnPqKg6M1y8shBGFAB7Cp18MNZk0tCs0vjCUZU2pYEg9snsKpN1HYwNKLdAs2CfBnHnkDHXIwc/OgHXCqfJqouVynGeOOlclvKOmJ2TR3d1DePLDp6MqSxjEj5yXwQg5HByfwNINUvZ7Vi8v223GyNm3fdGDz5cHHrRHjPo91axvbXMqqfEmkQnYcjgY7Ff29aR+3V4gEMQjCSOM48l7Z9Sf0qkp1hN+CZrsXmsm5SKTw0OYooxk57CtTpgkkl2vFcIcZYNJuA+vSlnsclpFYXF3cbcliMn+0AU2kubwQr7rBBDFgkEnGAe2POjbXw6uGPOzA9QsrgzKVEYXdzI6FsfIVGO0mntJY5Yim4EfCDsb1GelF2eoXcabrhIyBnlGzRJv1mA2g/EKn2wq4T0Rf6b6j7prM+jXC5W6BSJWGTvB+76Z/atncW8ePCWa3ttjcxg/9Pr19enbvXK9ZeXT9cN7bsUdJA6leMEV1DT7karbCZnjSSRAQ8i55PpTX804XOPAK0hkuJwgtozKvHilgRuyBgk9vn5V5fQyGxIFwLd1bckjgZYnoc5/nFPLDTxcmVEZo5ElRg4Ck4HIznrXomllu5ENvGY2XIURcnPduOtTAJrGKGxjSO9gieQf9VpCSgyOWBHHl/DRVnChV7YxOpeM4aG2di4XJ2DIx9a0Gk2E9lYpDvUnOSyDYCckjg0U0V4+Pi4Bz1HXz602MBzUhml2SCVQVJQsm3bjjbiiDGrWrrvG8rxz3NOtd0KeB3uI0ZosF3bJ4Pf8AalEK87MZJXp/PnUKWHROOTd2jLcWsU6nh0DfiKzH+oVpeXNjCbFtrK3h8tgEEfrnFPtJj2aXbpMcfD8I8xk4of2itxPo12pQSMIy4U+a8/tVYZFk9KvPetKtp41AEsSuc9Qe4+hyKZ25iWP7YR7s9wKyX+n1w82mzW9zF4ctvcNlPJH+MY9OTWqjihuV8TahwSvPoTRzHgDjf+nUeIL+Y92VfyrbSyr4cSkcYrK+wEYGhvLj78zH5gYrRX0jsUAQLgfz9K9GvFhzwj2LJVsdMj9BXhXBzx9ahEzhOSOp/Wq52bHPOTSFS1UU7SSOXwQOanIAoOOm4Dp8qHiUu67V/v6Zz2NXzqUhYkYO8CgwHsg2wxD/AHZNUsgjJLZwfLiibuLPu3PRTxSzXw401lwdxcLkGvP5P5s64/iQuJ5IHm+3lIGHYnnkDj9B+ArBavfvd38k0rFiDkZrYazfg2PhIAWEQaUn5YA+f+K55O+ZWx3rp4dJ8uJeG19iTFdafcRM3Il5X0OK1UNq1uHlCCZn7yICVGOgz0Fcl0++u9MuBNCcZPKnoRWrt/bqfwlR4lyPWluHvhbh5oU5RpL6yiumEk0LKQckoApbnocAcc1AMgmVUACKP+aQj2qnmbiMYPrXzX7spL8FuMA881Nyy3eX8F2u3dreORbyO85bDAjhRmuiaRbNHp1rA+fgiGc9RgZrKaR7L2cgS5jujNtcMQxHw+lb20u1upZ7dkSOWWPCHPHA6U7a+HNUU/8AolZSKk934zIy+HuG45HHNAW/tmtrdtA2liVmI3OjgAsexNW2aI1+6kErIpG31IrJajbmH2gvIxAhye7Y6gHPT1rJayTNyfbC7b/paTAoPA3XB/xQ0ntfqDY22lguc4yxbH5ikMYImkxCOcMPEcYAI46AedL7i52SssEMLsknRif8+gofuaN1XXcOkaZdSa3okjXSwbmLo3grgZ7dT6isYHMdwnBz04OOf+RWh9jr1fdriGQqHBVwo7cYPn5Ui1EbLqXDADxCy/jkVGnsjJY2jcRyGSBZWBywyM81WzK4G8ZGOnnQ+jSmbS4m6lRjpVgAdvhIIXrzWTwVoyHsi1zYe0l5aXWPBYNFCe58NjjP0J571pE1m2tokSaQq5G4gdskmsn7V+LpXtVb36hvCbY57bh9wj8AD9a0QnhjJjXTpXVMKG2g7uBVr/sRGV9h4WX2cg+zLgoznA82pndybpgcbhjv1H8zVPs3HNb6XZwLuB93i3gf/EZ/Wrp4JN7EL09f55V239JSRhfEClgASO9X+NAFBeROMZ5r42riBQVHQdTS26g8JjuPOM4XmlGDYriEzJtYnknofL/irLnEnhheVeYD8M0vtJcGPdbleDyTg9qYphpbVcHqW+XBoM2Er87Z4lHTYRnyoPVx4lmucZ3jqaPuf/7F4BISuVe0+u3Gp6jJFbyEWMDbIgOA+Orn1JzXGuN3bOjuolDj2iguIFkKq5gkVAxXt1rKeAd7ENx2zUpbiaWCJXkdsHozk4AqS5A/7OuPWuuZ6kqek3UGNY2Ge4PlVMkAxRMa5Y7uufyqQT4mXjFOI0LRI8LjY7Ajyq99TuGI3OCR0JGK8njyTgdDQ20ckjgeY60rQE2jaezuoT6Qq/1SyZEuiHiuMYO3Ax6FenrW0ZQ22SJvjGCDWT9htbtNR09/ZzXnHhMuLaaQgbf+3J6EcYP0pmLo6ZL7hcyr4kXCsDkOvmKhc+nXxciaxmqtpfEu4m/uOMnvWV9p4yNeQsCyyxrkjrnn09BTiPU47eAXPwSNjKRg8yMOQB9ay/tDq0l9q1utzBFFKkQkCqGBXJ5RgeQRihOi1PVhkl40UiqYUBIGS5yCQcZ/KkouydQkOIviYgYf9MmvZlSWYyho0cH7gBAz5dKmYYnlErTQiUHgggbiPmRQ8TMt6mx9lr1BqSxTkMJAYyi9jjOc/Q0b7T20Vte5AOxwpGO2eDST2cnB1OAzFcCRScAefXr61rPbCEbbaUY6Mh/WpJboaeNMj7K3G61uIFDHaAw9Kq0a2ls7t2MrNHKDuVuTuz1FC+y8u3Vdg6SIVx59xT+5hVCHjByppaTQu6Zf/Va28fRra7G9fAl2MV8mGP8AyC1p/Zi9ivdBsrlcYkiU/XGD+eaE1mJdX9nb6AoWdQzBR32nP7VlvZf2o0/TdNNrGT4SyEoCwGAQCRz6k1V3qWE+uBzbY32r9wBQv0GKDkmRtxBA586LMbSPlRwtCCEglXJBLYAx8hXdf0nPwulkRkA3DPzoOUnH9ox60bPHgDig3RsnIG30oBLbdRv+L+0Hv8qY24HvEXfap/Sg7VGcsoQbQvWj4mjgUzzHbGiMzN5ADk0rQRR7ZXv9O0yaYOEmaLbDnu5z0rkCZQ/fLeZPeiNY1e41jULi8nkkKyyFo0ZsiNf7QPpihQcjJNLEdQVXYvEm4EdOTU3mbt2FChqsPRKcCYckm9QepxyBRGfiDdPnS2POW2sVPai0uJFx4mD6mgMmTuFKFWI68EetC3CFhmj3bxEDZGQOc96p2gjBGMiihWBw/hirSxVCBwPIVEr4b47HvUnYAYNZoCeBre0eoLae7wv4b7drT5Jkx5A/2j5UtWTB346ng46VW3JyKJsZNjPG5AjkGGyM48qySRnTf011ndxXNqZ4vADiLMkR4bK449cjJGPWjr5Ibm1hmjgUuuANrdBzwfxFY2xupNNvFYbscrIMAhkJwfyrbC4t100Spbxyx7toYN14B/Dipc/1NFeH1UmDLfyQzxlCiEREHOCVwen5Cta1493ZK0h3JtEny865/dyid0ljiVEDbcZ5UkEnt0rX+zknvGlRZPQMhHnzUKnGPuona3Hu93FMuQ0bg/5rWzi6kAMZAB7VjmVmdtxwT1x51p11YPDAsYYOYQ3Trjg/pStGPNNnaHUbmOTlduSPQ/8ANcw1fT0tNVvLeRGBjmZRtUcjPH5V0O4uWjvo5lG33hPDIYdCDx+tZL/UbTLmT2iEtkuQ9vGZPRhkfoBT8P8ATBaNLvkjWQDbgr18u1CIXeUM3IDHn0z/AOqJdHiVgPtDn5Upe+1CBttvYJuX4S0kvB/DNdrIoPvL2ztY99xcYGcdD/ilS61pUsyIs7s7kDG0jv61nvaG5vfFIvoghIzshuSR+nWlEhhTafd2xtDZ8Zjk0DadQh5aQZymAMfnXntTNb6d7P30twUCG2eNULY3FuABQPshLLdWKmSNQCcJgEfDWV/1Xlc61b2/ibo0h3Bf+4nk1jGIC4wD2FS6CohNteO3lQAeiicZVKEU9PU0dj4AfKsjHiDMle7yGIbkV7Fzz3qQUFjmjhi213yF4xycZqCTgNtZtoBxg1O3YQTK/bPNe6pAom3ADa4yPnWwzZ7IokX4T8sVWcSLnHPQ0GjyQHIPHlRccoJBHQ9fnRTAQICfeFeMpFETJuT5UMjZGDQZi8yLJEm/mQcfMUw0u5cmO1I3K7DaO/XGKVjjpViHGMdR0PlS16FM11x7Parbxyk6ddKhcMuU8vp6+lOfZiK6t7WQXMLQr4gZd3lj/wBU+9mrm71z2Yt7q2vd93F9nPECM5HTI9Rg0dHpl9Ij7Y7csTkgMMj5jFcl099LTmGceG4nuGW02SHkjDj/ADTSG3uYbSJbravgyHa+QwIPajpvZhZYhvQwTEkbon8+9ARezupWMUpYmeFD0ZyQR5rS6HTzULj3edVl8ee1jfeC68oPIN3FEXssWo3T3NrNH4T4x9oATgAcg1VHps140dxbGOO3bh/tSVHoV7Uvu9D8W5kISKHBwUyfxoBCxOQOdrOT1Pzqlc5LTy4yScAAcUEdTVIw3wf/AGoefU2wZGVGKgDAySv84r0iBRrtvFcL9oZXIb4FTPT9/wD1RWh6VZvbzG6s47hg4UDIO0YBx5dTS25uTdOI1ZEkYbjhCTUGEsFuzRk9M8KwGcgZOa0+PQGummtdMtnuxD4MUETyMq56Lg9OnY1xLU9RvdVvnvb198r+XRR2UegroHtJqcMWh3UYQC4uohF8PQgjnj5E1zkkHndjNCn6ZEfEYjmoMea9YjPBzVlpaXN67raRGVkUuwBHwqOp5pDFUfLgdqYg8Ypdb8uO9HqaKMSiPB+dTDDNUA4FfB+abTBDYKkURk3FgAfvRH8qEBOMjmrrGTE5jP3ZVxRQAViDX0ffHSvpQVkZT2r1OlJ+mCY5MxkelB/dIqyNsM6+lRkHFFmJqc1YpoZTVqtQCa7/AE91f+m+0MQeQrBPhHIXOD2OPy+tdXvoGSGZrGV1mlPOYmX8OK/P6SFSGX7ynI+ddgsPaDxLKF5VYF4wdxHfH+a5+Wf0pDHL3s1vJCtxelJSoG7wWKyfMedX32tRWyYvLjYpHwgRN8Z8qz+ozJe2oXx5UwwIZSODQ+6a5lSJ4Glg27ZGYAg+vWoMoMYr4TO7QWF0olX7yqAp9cH9qrEeqxAIFkkXswZRx+NDR6dLYiT3JZ5ojgpDIRlT/wBp7UVHfarg7tKzzxiQD8aXsbDDuNibDcxpwPgBP5ccValvaqDNNfKM8ZCsfpnFL3sLlpd0gjB3ZJWUgDPYE1f7rfRRqCkDqGIA3ElenXjtXpHOH2IsjfpI15JvAK7ViLcdiSfSjNS91azYwyzMxI25jwOvnmg7HTykRmSeF5MZ2o/KjHTFB+08k9voNzseNGIUbl44z5+eKJjKe1kiXWtTvbSFoFwiZ7Y6/nSQxsO9TRNw658q+eMjyFABUQw4yKa+zLyrrCCMMcxyCUD/AGbTn5dqVnegOOcc11u0i07T/ZmU6aluUe1z4wjG5iV5Jb+flSMJye1BGM+VHLQsK7EX5Cr1NMjHzGpLVTmpRnisYvHSo7ijK46qcivAa8flTRMX34zKso6OKrU4FSLCW0XnlTjFUg8msY9DYuAfMVdHG8rLFGpZ3+FVAySfKhJTh1buK0nsPj/8nsJhtJiLSYPf4SP3pKfVNhS1mbOY2KlWDKcFWGCD5EVarV1D2ns7TWbWR72xWC4HKXMajeD9O3HQ/lXPpPZ7U0b4I1mTGd0bjkeoPIqfHyqlpmsAy3Xj6V0WGzZdBsmZgrmBcg5rFWGizPMouysSg52k5LenFdHOo2txYxxOqLsGP/jil5GGUKGjlezZldSEG7GfLmnulyWstuB4h3YHGeelKveVWB402hWU7s4OKF0hpYJIiJIxuwCcfTP886lyLwrLNnZT26qA7oMsBjuadL4QH3cfJazkP/7LuniwqUw/xHGee34VpIp4miUs4Bx59aghzicGqRSKDqFolxIHyp3leM56Hyoz+pw3V5HGthFFI55cMSe/X86sHs1bqRumlY46nFGW2jW8Mm9SxbPevTOUu8aQQbFCocjonP60j9q0lm0iWNXZyGDbSMcA81p4rJCOpA+dfNpCzlQ8vwg5wFpjYclVsYAqbZai9c019I1ee3lwVB3Rkd1PShFcs2T0rGKZVYDIHPaukwtbj2SBtsvD7t8OAcHqDn65rnTKzHrgZraew2seHpGsaJOdwkt5JrbK52tj4hn16/jSsxjc8CpqapQ/CvyqamiYlJUrcM52oCSOwqDc9Kv0w/atg4Y9Kxjw8da8zU7l1e4kKdM1XWMFaTDFc3Bt5rlbZWyfEZSwGBwMDz6UNg78YwR28qt02OObVLaGeYwxSSBWkAyVqd3FIkEc8wKO0rxFMY5UDnPcc4oMwHcD4c1bYzyxPFJEwSRGDK3karmwYxt6VXC+Dig/mGO6ey+ow6tpoeSFd8se4rjO096W3tstvcvkgqvQY6H5VmvZCZo7GOZLsxKrkFAhOfr0717q/vt5cGWKWRAT1Pf5Y+tcky4rCj+Dd4FFwJFmUqTyOlGSaErus8bB2K8rmsdHbahF0uQMdMmjodQ1m3iUSzsyHjeAQF+Zp6oVDK+jkt5cMCQylevGfrSsXs0CFbaUgADqnSqZ4rm7kKm9DHOfiYkZqP8ATWhDE3SuxPRVPFK2mhgi31e6hvA7tuLHJOMZreabqiNaqW/xXO/dpCQIkeQjyU8U6tLfUPBA8MjHYq2R+NRpDJn/2Q=='
    },
    {
        id: 4,
        name: 'Sere Eunice',
        role: 'Alto Coordinator',
        bio: 'Sere is a talented alto singer and coordinator, guiding our altos with her exceptional vocal skills and leadership.',
        avatar: 'https://down-id.img.susercontent.com/file/80630490e7ab777029796905b2058f85'
    },
    {
        id: 5,
        name: 'Ridho Michael Tondang',
        role: 'Tenor Coordinator',
        bio: 'Ridho is a skilled tenor singer and coordinator, leading our tenors with dedication and passion.',
        avatar: 'data:image/jpeg;base64,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'
    },
    {
        id: 6,
        name: 'Dhanu Harnoto',
        role: 'Bass Coordinator',
        bio: 'Dhanu is our bass coordinator, ensuring the deep harmonies are perfectly balanced. With a background in vocal performance, he brings a wealth of experience to the choir.',
        avatar: 'data:image/jpeg;base64,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'
    }
]

const achievements = [
    {
        year: 2025,
        title: 'World Expo Showcase',
        description: 'BMS represented Indonesia on the massive global cultural stage, performing at the Indonesia Pavilion for the World Expo in Osaka, Japan.'
    },
    {
        year: 2024,
        title: 'Grand Return to Taiwan',
        description: 'Marking exactly 20 years since their first invitation, BMS returned to headline the Taipei International Choral Music Festival.'
    },
    {
        year: 2023,
        title: 'Cultural Diplomacy in Singapore',
        description: 'Fresh off their EGP world title, BMS shifted to regional showcase diplomacy, performing a highly acclaimed, mesmerizing concert at the SOTA Concert Hall in Singapore.'
    },
    {
        year: 2022,
        title: 'EGP World Champions',
        description: 'BMS achieved the pinnacle of choral excellence by winning the prestigious European Grand Prix (EGP) in Graz, Austria, solidifying their status as one of the world\'s top choirs.'
    }
]
</script>

<style scoped>
.hero-section {
    background: linear-gradient(135deg, rgba(166, 123, 91, 0.08), rgba(193, 154, 107, 0.05));
    border: 1px solid rgba(166, 123, 91, 0.2);
}

.hero-image {
    width: 200px;
    height: 200px;
    margin: 0 auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 10px 40px rgba(166, 123, 91, 0.3);
}

.img-hero-about {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
}

.card-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-gradient);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.3);
}

.leader-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.2);
    object-fit: cover;
    object-position: center;
}

.leader-card {
    padding: 1.5rem;
    border-radius: 16px;
    background: rgba(166, 123, 91, 0.05);
    transition: all 0.4s ease;
}

.leader-card:hover {
    background: rgba(166, 123, 91, 0.12);
    transform: translateY(-5px);
}

.achievement-card {
    padding: 1.5rem;
    border-radius: 16px;
    background: rgba(166, 123, 91, 0.05);
    border-left: 4px solid var(--primary-color);
}

.year-badge {
    background: var(--primary-gradient);
    color: white;
    padding: 0.25rem 1rem;
    border-radius: 20px;
    display: inline-block;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.luxury-text-muted {
    color: var(--text-secondary);
}

.luxury-event-title {
    color: var(--text-primary);
}

.luxury-heading {
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .hero-image {
        width: 150px;
        height: 150px;
        margin-top: 2rem;
    }

    .stats-row {
        flex-wrap: wrap;
    }

    .stat {
        flex: 1 1 30%;
    }
}

@media (max-width: 576px) {
    .display-4 {
        font-size: 1.75rem;
    }

    .hero-image {
        width: 120px;
        height: 120px;
    }

    .hero-image .bi {
        font-size: 2rem !important;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .leader-avatar {
        width: 80px;
        height: 80px;
    }

    .leader-card {
        padding: 1rem;
    }

    .achievement-card {
        padding: 1rem;
    }
}
</style>