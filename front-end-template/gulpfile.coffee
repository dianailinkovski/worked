gulp = require("gulp")
gutil = require("gulp-util")
sass = require("gulp-ruby-sass")
csso = require("gulp-csso")
uglify = require("gulp-uglify")
jade = require("gulp-jade")
coffee = require("gulp-coffee")
concat = require("gulp-concat")
livereload = require("gulp-livereload") # Livereload plugin needed: https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei
tinylr = require("tiny-lr")
express = require("express")
app = express()
marked = require("marked") 
path = require("path")
server = tinylr()
es = require("event-stream")

gulp.task "css", ->
  gulp.src("src/styles/*.sass").pipe(sass()).pipe(csso()).pipe(gulp.dest("dist/styles/")).pipe livereload(server)

gulp.task "js", ->
  es.merge(gulp.src("src/scripts/*.coffee").pipe(coffee()), gulp.src("src/scripts/*.js")).pipe(uglify()).pipe(concat("all.min.js")).pipe(gulp.dest("dist/scripts/")).pipe livereload(server)

gulp.task "templates", ->
  gulp.src("src/*.jade").pipe(jade(pretty: true)).pipe(gulp.dest("dist/")).pipe livereload(server)

gulp.task "express", ->
  app.use express.static(path.resolve("./dist"))
  app.listen 1337
  gutil.log "Listening on port: 1337"

gulp.task "watch", ->
  server.listen 35729, (err) ->
    console.log(err)  if err
    gulp.watch "src/styles/*.sass", ["css"]
    gulp.watch "src/scripts/*.js", ["js"]
    gulp.watch "src/scripts/*.coffee", ["js"]
    gulp.watch "src/*.jade", ["templates"]


# Default Task
gulp.task "default", [
  "js"
  "css"
  "templates"
  "express"
  "watch"
]
