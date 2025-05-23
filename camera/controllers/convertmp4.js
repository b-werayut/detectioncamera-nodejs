// install first :  npm i @ffmpeg-installer/ffmpeg
const ffmpegPath = require('@ffmpeg-installer/ffmpeg').path;
const ffmpeg = require('fluent-ffmpeg');
ffmpeg.setFfmpegPath(ffmpegPath);
const path = require("path");
const { existsSync, mkdirSync } = require("fs");
const { glob } = require('glob');
const { insertVdoStatusLogs } = require('./DatabaseManage');

exports.convertToMp4Funct = async (inputFile, outputDir, beforetime, futuretime, directoryfm) => {
        // console.log('convertToMp4Funct inputfile =', inputFile)
        const vdofilecv = {fname:[],fcount:[]}
        const outputDirFormat = `${outputDir}/Vdo/`
        vdofilecv.fcount.push(
            inputFile.length
        )
        inputFile.map( items => {
        if (!existsSync(items)) {
            return reject(new Error("Input file does not exist"));
        }
        vdofilecv.fname.push(
            items
        )
       
        const outputFileName = path.basename(items, path.extname(items)) + ".mp4";
        const outputFilePath = path.join(outputDirFormat, outputFileName);

         ffmpeg(items)
            .output(outputFilePath)
            .on("start", (commandLine) => {
                // console.log("FFmpeg command: " + commandLine);
            })
             .on("progress", (progress) => {
                if(progress == ''){
                    console.log(`Processing`);
                }
            })
            .on("end", () => {
                // console.log(`Conversion completed: ${outputFilePath}`);
            })
            .on("error", (err) => {
                console.error("Error: ", err);
            })
            .run();
    })

    return ({vdofilecv: vdofilecv, msg: 'Convert vdo success'})
}

