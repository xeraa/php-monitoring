# PHP Monitoring

Monitor your PHP application with logs, metrics, pings, and traces. Slides: [https://speakerdeck.com/xeraa/monitor-your-php-application-with-the-elastic-stack](https://speakerdeck.com/xeraa/monitor-your-php-application-with-the-elastic-stack)



## Features

1. Quick overview of what is running in Kibana's monitoring view.
1. **Metricbeat System**:
    1. Show the *[Metricbeat System] Overview* dashboard in Kibana.
    1. Then switch to *[Metricbeat System] Host overview* and see the spike.
    1. Build a visualization with Time Series Visual Builder to find out what is going on: `system.memory.used.bytes` per `beat.name` and `system.process.memory.rss.bytes` per `system.process.name` sorted by the `Sum of system.process.memory.rss.bytes`.
1. **Packetbeat**: Let attendees hit the CMS with a few requests.
    1. Show the *[Packetbeat] Overview* and *[Packetbeat] Flows*.
    1. Explain why *[Packetbeat] HTTP* is empty.
    1. Hit a URL with a bad certificate (like frontend) and filter down in the Packetbeat Discover view to `type: "tls"` and `status: "Error"`.
1. **Filebeat modules**:
    1. Show the *[Filebeat Nginx] Overview* and *[Filebeat Nginx] Access and error logs* dashboards.
    1. Show the *[Filebeat MySQL] Overview* dashboard.
    1. Show the *[Filebeat System] SSH login attempts*, *[Filebeat System] Sudo commands*, and *[Filebeat System] Syslog dashboard* dashboards.
1. Run `./ab.sh` on the backend instance to get a more interesting view of the *[Filebeat Nginx] Overview* and *[Packetbeat] MySQL performance* dashboards.
1. **Metricbeat modules**:
    1. Show the *[Metricbeat Nginx] Overview* dashboard based on [https://xeraa.wtf/server-status](https://xeraa.wtf/server-status).
    1. Show the *[Metricbeat MySQL] Overview* dashboard.
    1. Build a Time Series Visual Builder visualization for [https://xeraa.wtf/status](https://xeraa.wtf/status): Sum of `php_fpm.pool.connections.accepted` (optionally the derivative of this value), sum of `php_fpm.pool.connections.queued`, and sum of `php_fpm_pool.process.active` on a different axis and as a bar.
    1. Add annotations to the previous visualizations — they don't correlate in this example, but it is still handy to see.
1. **Filebeat**: Collecting both */var/www/html/silverstripe/silverstripe.log* and */var/www/html/silverstripe/silverstripe.json*. Hit [https://xeraa.wtf/error/](https://xeraa.wtf/error/), [https://xeraa.wtf/error/server/](https://xeraa.wtf/error/server/), and [https://xeraa.wtf/error/client/](https://xeraa.wtf/error/client/) for different errors and find them in the logs.
1. **Heartbeat**: Run Heartbeat and show the *Heartbeat HTTP monitoring* dashboard in Kibana, then stop either nginx or php-fpm (different response code).
1. **Auditbeat**: Show the dashboards for *[Auditbeat Auditd] Overview* and *[Auditbeat File Integrity] Overview*.
1. **Kibana Dashboard Mode**: Point attendees to the Kibana instance to let them play around on their own.



## Setup

1. Make sure you have your AWS account set up, access key created, and added as environment variables in `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY`. Protip: Use [https://github.com/sorah/envchain](https://github.com/sorah/envchain) to keep your environment variables safe.
1. Create the Elastic Cloud instance with the same version as specified in *variables.yml*'s `elastic_version`, enable Kibana as well as the GeoIP & user agent plugins, and set the environment variables with the values for `ELASTICSEARCH_HOST`, `ELASTICSEARCH_USER`, `ELASTICSEARCH_PASSWORD`, as well as `KIBANA_HOST`, `KIBANA_ID`.
1. Change into the *lightsail/* directory.
1. Change the settings to a domain you have registered under Route53 in *inventory*, *variables.tf*, and *variables.yml*. Set the Hosted Zone for that domain and export the Zone ID under the environment variable `TF_VAR_zone_id`. If you haven't created the Hosted Zone yet, you should set it up in the AWS Console first and then set the environment variable.
1. If you haven't installed the AWS plugin for Terraform, get it with `terraform init` first. Then create the keypair, DNS settings, and instances with `terraform apply`.
1. Open HTTPS on the network configuration on all instances as well as MySQL (3306) APM server (8200) on the backend one (waiting for this [Terraform issue](https://github.com/terraform-providers/terraform-provider-aws/issues/700) to automate that step).
1. Apply the base configuration to all instances with `ansible-playbook --inventory-file=inventory configure_all.yml`.
1. Apply the instance specific configuration with `ansible-playbook --inventory-file=inventory configure_frontend.yml` and `ansible-playbook --inventory-file=inventory configure_backend.yml`.
1. Deploy the JAR with `ansible-playbook --inventory-file=inventory deploy_bad.yml` (Ansible is also building it) and `ansible-playbook --inventory-file=inventory deploy_frontend.yml`.

When you are done, remove the instances, DNS settings, and key with `terraform destroy`.



## Todo

* Switch to: metricbeat keystore create && metricbeat keystore add output.elasticsearch.password
* Fix: osquery
* Change: Alerting example
* APM: https://github.com/philkra/elastic-apm-php-agent
