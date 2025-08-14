const express = require('express')
const app = express()
const morgan = require('morgan')
const { readdirSync } = require('fs')
const cors = require('cors')
const { cronDelDir } = require('./controllers/directory')

app.use(morgan('dev'))
app.use(cors())

readdirSync('./routes')
    .map((c) => app.use('/api', require(`./routes/${c}`)))

app.listen(3000,
    async () => {
        await cronDelDir();
        console.log('Server is running on port 3000. ')
    })

